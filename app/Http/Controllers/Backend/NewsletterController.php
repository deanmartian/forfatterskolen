<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Jobs\SendNewsletterJob;
use App\Models\Newsletter;
use App\Services\NewsletterService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $newsletters = Newsletter::orderBy('created_at', 'desc')->paginate(20);

        return view('backend.newsletter.index', compact('newsletters'));
    }

    public function create()
    {
        return view('backend.newsletter.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'segment' => 'required|string',
        ]);

        Newsletter::create([
            'subject' => $request->subject,
            'preview_text' => $request->preview_text,
            'body_html' => $request->body_html,
            'from_address' => $request->from_address ?? 'post@nyhetsbrev.forfatterskolen.no',
            'from_name' => $request->from_name ?? 'Forfatterskolen',
            'segment' => $request->segment,
            'status' => 'draft',
        ]);

        return redirect()->route('admin.newsletter.index')->with('success', 'Nyhetsbrev opprettet som utkast.');
    }

    public function edit($id)
    {
        $newsletter = Newsletter::findOrFail($id);

        if (! $newsletter->isDraft() && ! $newsletter->isScheduled()) {
            return back()->with('error', 'Kan ikke redigere et sendt nyhetsbrev.');
        }

        return view('backend.newsletter.edit', compact('newsletter'));
    }

    public function update($id, Request $request)
    {
        $newsletter = Newsletter::findOrFail($id);

        $request->validate([
            'subject' => 'required|string|max:255',
            'body_html' => 'required|string',
            'segment' => 'required|string',
        ]);

        $newsletter->update($request->only([
            'subject', 'preview_text', 'body_html',
            'from_address', 'from_name', 'segment',
        ]));

        return redirect()->route('admin.newsletter.index')->with('success', 'Nyhetsbrev oppdatert.');
    }

    public function send($id, Request $request, NewsletterService $service)
    {
        $newsletter = Newsletter::findOrFail($id);

        if ($request->has('scheduled_at') && $request->scheduled_at) {
            $service->schedule($newsletter, Carbon::parse($request->scheduled_at));

            return back()->with('success', 'Nyhetsbrev planlagt for ' . $newsletter->scheduled_at->format('d.m.Y H:i'));
        }

        $service->sendNow($newsletter);
        SendNewsletterJob::dispatch($newsletter->id);

        return back()->with('success', 'Nyhetsbrev-utsending startet!');
    }

    public function preview($id)
    {
        $newsletter = Newsletter::findOrFail($id);

        return view('emails.branded.newsletter', ['body' => $newsletter->body_html]);
    }

    public function duplicate($id)
    {
        $original = Newsletter::findOrFail($id);

        $copy = $original->replicate();
        $copy->subject = 'Kopi av: ' . $original->subject;
        $copy->status = 'draft';
        $copy->scheduled_at = null;
        $copy->sent_at = null;
        $copy->total_recipients = 0;
        $copy->total_sent = 0;
        $copy->total_failed = 0;
        $copy->save();

        return redirect()->route('admin.newsletter.edit', $copy->id)->with('success', 'Nyhetsbrev duplisert.');
    }

    public function sendTest($id, Request $request)
    {
        $newsletter = Newsletter::findOrFail($id);

        $testEmail = $request->input('test_email', auth()->user()->email);
        $resendKey = config('services.resend.key');

        if (empty($resendKey)) {
            return back()->with('error', 'RESEND_API_KEY er ikke konfigurert.');
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $resendKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.resend.com/emails', [
            'from' => "{$newsletter->from_name} <{$newsletter->from_address}>",
            'reply_to' => 'post@forfatterskolen.no',
            'to' => [$testEmail],
            'subject' => '[TEST] ' . $newsletter->subject,
            'html' => $newsletter->body_html,
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Resend feil: ' . $response->body());
        }

        return back()->with('success', "Test-e-post sendt til {$testEmail} via Resend.");
    }

    public function stats($id)
    {
        $newsletter = Newsletter::withCount([
            'sends as total_sends',
            'sends as sent_count' => fn ($q) => $q->where('status', 'sent'),
            'sends as pending_count' => fn ($q) => $q->where('status', 'pending'),
            'sends as failed_count' => fn ($q) => $q->where('status', 'failed'),
        ])->findOrFail($id);

        return view('backend.newsletter.stats', compact('newsletter'));
    }

    public function destroy($id)
    {
        $newsletter = Newsletter::findOrFail($id);

        if ($newsletter->isSending()) {
            return back()->with('error', 'Kan ikke slette et nyhetsbrev som sendes.');
        }

        $newsletter->sends()->delete();
        $newsletter->delete();

        return redirect()->route('admin.newsletter.index')->with('success', 'Nyhetsbrev slettet.');
    }
}
