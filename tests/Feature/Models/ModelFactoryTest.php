<?php

namespace Tests\Feature\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Tag;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketMessage;
use App\Models\TicketStatusLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class ModelFactoryTest extends TestCase
{
    use RefreshDatabase;

    // -- User --

    public function test_user_factory_creates_valid_record(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertSame(UserRole::Customer, $user->role);
    }

    public function test_user_factory_default_role_without_refresh(): void
    {
        $user = User::factory()->create();

        $this->assertSame(UserRole::Customer, $user->role);
        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isAgent());
        $this->assertFalse($user->isAdmin());
    }

    public function test_user_factory_agent_role_without_refresh(): void
    {
        $agent = User::factory()->agent()->create();

        $this->assertSame(UserRole::Agent, $agent->role);
        $this->assertTrue($agent->isAgent());
        $this->assertFalse($agent->isCustomer());
        $this->assertFalse($agent->isAdmin());
    }

    public function test_user_factory_admin_role_without_refresh(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isAgent());
        $this->assertFalse($admin->isCustomer());
    }

    public function test_user_factory_agent_state(): void
    {
        $agent = User::factory()->agent()->create();

        $this->assertSame(UserRole::Agent, $agent->role);
        $this->assertTrue($agent->isAgent());
        $this->assertFalse($agent->isCustomer());
        $this->assertFalse($agent->isAdmin());
    }

    public function test_user_factory_admin_state(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertSame(UserRole::Admin, $admin->role);
        $this->assertTrue($admin->isAdmin());
    }

    // -- Ticket --

    public function test_ticket_factory_creates_valid_record(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();

        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
        $this->assertNotNull($ticket->subject);
        $this->assertSame(TicketStatus::Open, $ticket->status);
        $this->assertSame(TicketPriority::Normal, $ticket->priority);
        $this->assertNull($ticket->number);
    }

    public function test_ticket_status_and_priority_casts(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create([
            'status' => TicketStatus::Pending,
            'priority' => TicketPriority::Urgent,
        ]);

        $this->assertSame(TicketStatus::Pending, $ticket->status);
        $this->assertSame(TicketPriority::Urgent, $ticket->priority);
    }

    public function test_ticket_customer_relationship(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();

        $this->assertInstanceOf(User::class, $ticket->customer);
        $this->assertSame($customer->id, $ticket->customer->id);
    }

    public function test_ticket_assignee_relationship(): void
    {
        $customer = User::factory()->create();
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create(['assignee_id' => $agent->id]);

        $this->assertInstanceOf(User::class, $ticket->assignee);
        $this->assertSame($agent->id, $ticket->assignee->id);
    }

    public function test_ticket_pending_state(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->pending()->create();

        $this->assertSame(TicketStatus::Pending, $ticket->status);
    }

    public function test_ticket_closed_state(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->closed()->create();

        $this->assertSame(TicketStatus::Closed, $ticket->status);
        $this->assertNotNull($ticket->closed_at);
    }

    // -- TicketMessage --

    public function test_ticket_message_factory_creates_valid_record(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $message = TicketMessage::factory()->for($ticket)->for($customer)->create();

        $this->assertDatabaseHas('ticket_messages', ['id' => $message->id]);
        $this->assertNotNull($message->body);
        $this->assertFalse($message->is_internal);
    }

    public function test_ticket_message_internal_state(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $message = TicketMessage::factory()->for($ticket)->for($customer)->internal()->create();

        $this->assertTrue($message->is_internal);
    }

    public function test_ticket_message_relationships(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $message = TicketMessage::factory()->for($ticket)->for($customer)->create();

        $this->assertInstanceOf(Ticket::class, $message->ticket);
        $this->assertInstanceOf(User::class, $message->user);
    }

    // -- TicketAttachment --

    public function test_ticket_attachment_factory_creates_valid_record(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $message = TicketMessage::factory()->for($ticket)->for($customer)->create();
        $attachment = TicketAttachment::factory()->for($message, 'message')->create();

        $this->assertDatabaseHas('ticket_attachments', ['id' => $attachment->id]);
        $this->assertNotNull($attachment->path);
        $this->assertNotNull($attachment->original_name);
    }

    public function test_ticket_attachment_message_relationship(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $message = TicketMessage::factory()->for($ticket)->for($customer)->create();
        $attachment = TicketAttachment::factory()->for($message, 'message')->create();

        $this->assertInstanceOf(TicketMessage::class, $attachment->message);
        $this->assertSame($message->id, $attachment->message->id);
    }

    // -- Tag --

    public function test_tag_factory_creates_valid_record(): void
    {
        $tag = Tag::factory()->create();

        $this->assertDatabaseHas('tags', ['id' => $tag->id]);
        $this->assertNotNull($tag->name);
    }

    // -- TicketStatusLog --

    public function test_ticket_status_log_factory_creates_valid_record(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $log = TicketStatusLog::factory()->for($ticket)->for($customer)->create();

        $this->assertDatabaseHas('ticket_status_logs', ['id' => $log->id]);
        $this->assertInstanceOf(TicketStatus::class, $log->from_status);
        $this->assertInstanceOf(TicketStatus::class, $log->to_status);
    }

    public function test_ticket_status_log_created_at_is_carbon(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $log = TicketStatusLog::factory()->for($ticket)->for($customer)->create();

        $this->assertInstanceOf(CarbonImmutable::class, $log->created_at);
    }

    public function test_ticket_status_log_relationships(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $log = TicketStatusLog::factory()->for($ticket)->for($customer)->create();

        $this->assertInstanceOf(Ticket::class, $log->ticket);
        $this->assertInstanceOf(User::class, $log->user);
    }

    // -- User relationships --

    public function test_user_tickets_as_customer_relationship(): void
    {
        $customer = User::factory()->create();
        Ticket::factory()->count(3)->for($customer, 'customer')->create();

        $this->assertCount(3, $customer->ticketsAsCustomer);
    }

    public function test_user_tickets_as_assignee_relationship(): void
    {
        $customer = User::factory()->create();
        $agent = User::factory()->agent()->create();
        Ticket::factory()->count(2)->for($customer, 'customer')->create(['assignee_id' => $agent->id]);

        $this->assertCount(2, $agent->ticketsAsAssignee);
    }

    public function test_user_messages_relationship(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        TicketMessage::factory()->count(2)->for($ticket)->for($customer)->create();

        $this->assertCount(2, $customer->messages);
    }

    // -- Ticket relationships --

    public function test_ticket_messages_relationship(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        TicketMessage::factory()->count(3)->for($ticket)->for($customer)->create();

        $this->assertCount(3, $ticket->messages);
    }

    public function test_ticket_tags_relationship(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        $tags = Tag::factory()->count(2)->create();
        $ticket->tags()->attach($tags);

        $this->assertCount(2, $ticket->tags);
    }

    public function test_ticket_status_logs_relationship(): void
    {
        $customer = User::factory()->create();
        $ticket = Ticket::factory()->for($customer, 'customer')->create();
        TicketStatusLog::factory()->count(2)->for($ticket)->for($customer)->create();

        $this->assertCount(2, $ticket->statusLogs);
    }
}
