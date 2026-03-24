<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\EmailAutomationQueue;
use App\Models\EmailSequence;
use App\Models\Newsletter;
use App\Services\ContactService;
use Illuminate\Http\Request;

class CrmController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * CRM Dashboard — standard-fane er Kontakter
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'contacts');

        $data = [
            'tab' => $tab,
            'totalContacts' => Contact::count(),
            'activeContacts' => Contact::subscribed()->count(),
            'sequences' => EmailSequence::withCount('steps')->get(),
            'pendingEmails' => EmailAutomationQueue::pending()->count(),
        ];

        return view('backend.crm.index', $data);
    }

    /**
     * Kontakter-fane med søk og filtrering
     */
    public function contacts(Request $request)
    {
        $query = Contact::with('tags');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($source = $request->get('source')) {
            $query->where('source', $source);
        }

        if ($tag = $request->get('tag')) {
            $query->withTag($tag);
        }

        $contacts = $query->orderBy('created_at', 'desc')->paginate(50);

        if ($request->ajax()) {
            return view('backend.crm.tabs.contacts', compact('contacts'));
        }

        return view('backend.crm.index', [
            'tab' => 'contacts',
            'contacts' => $contacts,
            'totalContacts' => Contact::count(),
            'activeContacts' => Contact::subscribed()->count(),
            'sequences' => EmailSequence::withCount('steps')->get(),
            'pendingEmails' => EmailAutomationQueue::pending()->count(),
        ]);
    }

    /**
     * Vis en enkelt kontakt
     */
    public function contactShow($id)
    {
        $contact = Contact::with(['tags', 'automationQueue.sequence', 'automationQueue.step', 'exclusions'])->findOrFail($id);

        return view('backend.crm.contact-show', compact('contact'));
    }

    /**
     * Legg til tag på kontakt
     */
    public function addTag($id, Request $request, ContactService $contactService)
    {
        $contact = Contact::findOrFail($id);
        $contactService->tagContact($contact, $request->input('tag'));

        return back()->with('success', 'Tag lagt til.');
    }

    /**
     * Fjern tag fra kontakt
     */
    public function removeTag($id, $tag, ContactService $contactService)
    {
        $contact = Contact::findOrFail($id);
        $contactService->removeTag($contact, $tag);

        return back()->with('success', 'Tag fjernet.');
    }

    /**
     * Meld av kontakt
     */
    public function unsubscribeContact($id, ContactService $contactService)
    {
        $contact = Contact::findOrFail($id);
        $contactService->unsubscribe($contact);

        return back()->with('success', 'Kontakt avmeldt.');
    }

    /**
     * Oppdater kontaktinfo
     */
    public function updateContact($id, Request $request)
    {
        $contact = Contact::findOrFail($id);

        $request->validate([
            'email' => 'required|email',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
        ]);

        $oldEmail = $contact->email;

        // Sjekk om ny e-post allerede finnes
        if ($request->email !== $oldEmail) {
            $existing = Contact::where('email', $request->email)->where('id', '!=', $contact->id)->first();
            if ($existing) {
                return back()->with('error', "E-postadressen {$request->email} er allerede registrert på kontakt #{$existing->id} ({$existing->fullName()}).");
            }
        }

        $contact->update($request->only(['email', 'first_name', 'last_name', 'phone']));

        // Oppdater også ventende e-poster i køen hvis e-post endret
        if ($oldEmail !== $request->email) {
            \DB::table('email_automation_queue')
                ->where('contact_id', $contact->id)
                ->where('status', 'pending')
                ->update(['email' => $request->email]);
        }

        return back()->with('success', 'Kontaktinfo oppdatert.');
    }

    /**
     * Planlagte e-poster
     */
    public function planned(Request $request)
    {
        $planned = EmailAutomationQueue::pending()
            ->with(['contact', 'sequence', 'step'])
            ->orderBy('scheduled_at')
            ->paginate(50);

        if ($request->ajax()) {
            return view('backend.crm.tabs.planned', compact('planned'));
        }

        return view('backend.crm.index', [
            'tab' => 'planned',
            'planned' => $planned,
            'totalContacts' => Contact::count(),
            'activeContacts' => Contact::subscribed()->count(),
            'sequences' => EmailSequence::withCount('steps')->get(),
            'pendingEmails' => EmailAutomationQueue::pending()->count(),
        ]);
    }

    /**
     * Kanseller planlagt e-post
     */
    public function cancelPlanned($id)
    {
        $item = EmailAutomationQueue::findOrFail($id);
        $item->cancel('admin_cancelled');

        return back()->with('success', 'E-post kansellert.');
    }

    /**
     * Sendt historikk
     */
    public function history(Request $request)
    {
        $query = EmailAutomationQueue::where('status', 'sent')
            ->with(['contact', 'sequence', 'step'])
            ->orderBy('sent_at', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                    ->orWhereHas('step', fn ($q2) => $q2->where('subject', 'like', "%{$search}%"));
            });
        }

        $history = $query->paginate(50);

        if ($request->ajax()) {
            return view('backend.crm.tabs.history', compact('history'));
        }

        return view('backend.crm.index', [
            'tab' => 'history',
            'history' => $history,
            'totalContacts' => Contact::count(),
            'activeContacts' => Contact::subscribed()->count(),
            'sequences' => EmailSequence::withCount('steps')->get(),
            'pendingEmails' => EmailAutomationQueue::pending()->count(),
        ]);
    }

    /**
     * Statistikk
     */
    public function statistics()
    {
        $stats = [
            'total_contacts' => Contact::count(),
            'active_contacts' => Contact::where('status', 'active')->count(),
            'unsubscribed' => Contact::where('status', 'unsubscribed')->count(),
            'bounced' => Contact::where('status', 'bounced')->count(),
            'contacts_by_source' => Contact::selectRaw('source, COUNT(*) as count')->groupBy('source')->pluck('count', 'source'),
            'emails_sent_today' => EmailAutomationQueue::where('status', 'sent')->whereDate('sent_at', today())->count(),
            'emails_sent_week' => EmailAutomationQueue::where('status', 'sent')->where('sent_at', '>=', now()->subWeek())->count(),
            'emails_sent_month' => EmailAutomationQueue::where('status', 'sent')->where('sent_at', '>=', now()->subMonth())->count(),
            'emails_cancelled' => EmailAutomationQueue::where('status', 'cancelled')->count(),
            'emails_pending' => EmailAutomationQueue::where('status', 'pending')->count(),
            'newsletters_sent' => Newsletter::where('status', 'sent')->count(),
            'sequences' => EmailSequence::withCount(['steps', 'queueItems'])->get(),
        ];

        return view('backend.crm.index', [
            'tab' => 'statistics',
            'stats' => $stats,
            'totalContacts' => $stats['total_contacts'],
            'activeContacts' => $stats['active_contacts'],
            'sequences' => $stats['sequences'],
            'pendingEmails' => $stats['emails_pending'],
        ]);
    }
}
