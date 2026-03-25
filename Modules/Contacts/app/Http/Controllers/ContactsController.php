<?php

namespace Modules\Contacts\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;

class ContactsController extends Controller
{
    public function destroyAccount(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('contacts.delete'), 403);

        Account::query()->whereKey($id)->delete();

        return redirect()->route('accounts.index')->with('status', 'Account deleted.');
    }

    public function destroyContact(string $id): RedirectResponse
    {
        abort_unless(auth()->user()?->can('contacts.delete'), 403);

        Contact::query()->whereKey($id)->delete();

        return redirect()->route('contacts.index')->with('status', 'Contact deleted.');
    }
}
