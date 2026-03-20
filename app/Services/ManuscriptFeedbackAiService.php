<?php

namespace App\Services;

use App\FreeManuscript;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManuscriptFeedbackAiService
{
    /**
     * Generate AI feedback draft for a free manuscript.
     */
    public function generateFeedback(string $manuscriptContent, string $name, ?string $genre = null, ?string $email = null): string
    {
        $genreText = $genre ? "Sjangeren er: {$genre}." : '';
        $examples = $this->getTrainingExamples();
        $studentContext = $email ? $this->getStudentContext($email) : '';

        $systemPrompt = <<<PROMPT
Du er en erfaren og varm redaktør ved Forfatterskolen, Norges ledende skriveskole.
Din oppgave er å skrive en profesjonell, varm og oppmuntrende tilbakemelding på en innsendt tekst.

Forfatterskolen tilbyr følgende aktive kurs som du KAN nevne (bruk kun disse, ikke finn på kurs):
{$this->getActiveCourses()}
I tillegg tilbyr Forfatterskolen manusutvikling - profesjonell tilbakemelding på manus fra erfarne redaktører (https://www.forfatterskolen.no/manusutvikling).
Manusutvikling er spesielt relevant å anbefale for de som har skrevet lengre tekster eller har et manus under arbeid.

VIKTIG: Nevn BARE kurs fra listen over. Ikke nevn "Skriveverksted", "Novellekurs" eller andre kurs som ikke finnes i listen.
Når du anbefaler et kurs eller manusutvikling, lag en klikkbar HTML-lenke, f.eks:
- Kurs: <a href="https://www.forfatterskolen.no/kurs/119">Årskurs 2026</a>
- Manusutvikling: <a href="https://www.forfatterskolen.no/manusutvikling">manusutvikling</a>

Tilbakemeldingen skal følge denne strukturen:
1. Takk personen for at de har sendt inn teksten sin.
2. Anerkjenn motet det krever å dele arbeidet sitt.
3. Fremhev styrker i teksten (det som fungerer godt).
4. Pek på forbedringsområder (struktur, språk, karakterer, dialog, spenning, tempo, osv.).
5. Gi spesifikke eksempler fra teksten for å underbygge poengene dine.
6. Oppmuntre personen til å fortsette å skrive.
7. Avslutt med en myk og naturlig omtale av at Forfatterskolens kurs kan hjelpe dem å utvikle seg videre som forfatter.

Retningslinjer:
- Skriv på norsk bokmål.
- Vær varm, oppmuntrende og profesjonell, men ærlig.
- Analyser teksten for: fortellerstemme, karakterutvikling, dialog, struktur, språkbruk, tempo, konflikt og tematikk.
- Gi konkrete eksempler fra den faktiske teksten.
- Tilbakemeldingen skal være 500-800 ord.
- Formater som HTML med <p>-tagger, <strong> for uthevinger og <em> for kursiv.
- IKKE bruk <html>, <head> eller <body>-tagger. Bare innholdet.
- Bruk personens fornavn i tilbakemeldingen.

Her er eksempler på tidligere tilbakemeldinger som viser stilen og tonen vi bruker:

{$examples}

Bruk samme tone, struktur og varme som eksemplene over. Tilpass tilbakemeldingen til den konkrete teksten du får.

{$studentContext}
PROMPT;

        $userMessage = "Skriv en tilbakemelding på denne teksten.\n\nForfatterens fornavn: {$name}\n{$genreText}\n\nTeksten:\n\n{$manuscriptContent}";

        $apiKey = config('services.anthropic.key');

        if (empty($apiKey)) {
            throw new \Exception('Anthropic API key is not configured.');
        }

        $response = Http::withHeaders([
            'x-api-key' => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ])->timeout(120)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-opus-4-20250514',
            'max_tokens' => 4096,
            'system' => $systemPrompt,
            'messages' => [
                ['role' => 'user', 'content' => $userMessage],
            ],
        ]);

        if ($response->failed()) {
            Log::error('Anthropic API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Feil ved generering av AI-tilbakemelding. Prøv igjen senere.');
        }

        $data = $response->json();

        return $data['content'][0]['text'] ?? '';
    }

    /**
     * Check if person is already a student and what courses they have.
     */
    private function getStudentContext(string $email): string
    {
        $user = \App\User::where('email', $email)->first();
        if (!$user) {
            return 'Personen er IKKE registrert som elev. Du kan anbefale alle aktive kurs og manusutvikling.';
        }

        $coursesTaken = $user->coursesTaken()->with('package')->get();
        $courseNames = [];
        foreach ($coursesTaken as $ct) {
            if ($ct->package && $ct->package->course) {
                foreach ($ct->package->course as $courseId) {
                    $course = \DB::table('courses')->where('id', $courseId)->first();
                    if ($course) $courseNames[] = $course->title;
                }
            }
        }

        $manuscripts = \DB::table('shop_manuscripts_taken')
            ->where('user_id', $user->id)
            ->count();

        $info = "VIKTIG ELEVINFO: Personen ({$email}) er allerede registrert som elev.\n";
        if (!empty($courseNames)) {
            $info .= "Kurs eleven allerede har: " . implode(', ', array_unique($courseNames)) . "\n";
            $info .= "IKKE anbefal kurs eleven allerede har! Anbefal andre relevante kurs i stedet.\n";
        }
        if ($manuscripts > 0) {
            $info .= "Eleven har allerede brukt manusutvikling ({$manuscripts} gang(er)).\n";
            $info .= "Du kan gjerne anbefale manusutvikling igjen, men referer til at de kjenner tjenesten.\n";
        }

        return $info;
    }

    /**
     * Get active courses from database.
     */
    private function getActiveCourses(): string
    {
        $courses = \DB::table('courses')
            ->select('id', 'title')
            ->where('status', 1)
            ->where('for_sale', 1)
            ->whereNotIn('id', [43, 108, 113]) // skip free courses
            ->get();

        return $courses->map(fn($c) => "- {$c->title} (https://www.forfatterskolen.no/kurs/{$c->id})")->implode("\n");
    }

    /**
     * Get training examples from archived feedback.
     */
    private function getTrainingExamples(): string
    {
        $examples = FreeManuscript::where('is_feedback_sent', 1)
            ->whereNotNull('feedback_content')
            ->where('feedback_content', '!=', '')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        $output = '';
        foreach ($examples as $i => $example) {
            $num = $i + 1;
            $manuscriptExcerpt = Str::limit(strip_tags($example->content), 300);
            $feedbackExcerpt = Str::limit(strip_tags(html_entity_decode($example->feedback_content)), 600);
            $output .= "--- EKSEMPEL {$num} ---\n";
            $output .= "Tekst (utdrag): {$manuscriptExcerpt}\n";
            $output .= "Tilbakemelding: {$feedbackExcerpt}\n\n";
        }

        return $output;
    }
}
