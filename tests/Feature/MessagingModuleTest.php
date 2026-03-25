<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Messaging\Livewire\MessageComposer;
use Modules\Messaging\Services\MessagingService;
use Modules\Users\Models\Role;

function makeMessagingRole(): Role
{
    return Role::query()->create([
        'name' => 'Messaging Admin '.str()->random(6),
        'can_view' => true,
        'can_create' => true,
        'can_edit' => true,
        'can_delete' => true,
        'can_export' => true,
        'record_visibility' => 'all',
        'module_access' => ['messaging' => true, 'users' => true, 'core' => true],
    ]);
}

test('authorized users can open messaging page', function () {
    $role = makeMessagingRole();

    $user = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($user)
        ->get(route('messages.index'))
        ->assertOk()
        ->assertSee('Channels');
});

test('messaging service creates direct channel and sends a message', function () {
    $role = makeMessagingRole();

    $sender = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $receiver = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $service = app(MessagingService::class);
    $channel = $service->createDirectChannel($sender, $receiver);
    $service->sendMessage($channel, $sender, 'Hello @'.$receiver->full_name);

    $this->assertDatabaseHas('channels', [
        'id' => $channel->id,
        'type' => 'Direct',
    ]);

    $this->assertDatabaseHas('messages', [
        'channel_id' => $channel->id,
        'sender_id' => $sender->id,
        'body' => 'Hello @'.$receiver->full_name,
    ]);
});

test('message composer livewire action sends a message', function () {
    $role = makeMessagingRole();

    $sender = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $receiver = User::factory()->create([
        'role_id' => $role->id,
    ]);

    $this->actingAs($sender);

    $service = app(MessagingService::class);
    $channel = $service->createDirectChannel($sender, $receiver);

    Livewire::test(MessageComposer::class, ['channelId' => (string) $channel->id])
        ->set('body', 'Composer says hello')
        ->call('send');

    $this->assertDatabaseHas('messages', [
        'channel_id' => $channel->id,
        'sender_id' => $sender->id,
        'body' => 'Composer says hello',
    ]);
});
