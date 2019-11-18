<?php 

declare(strict_types=1);

use Traya\AggregateRoot;
use Traya\CommitAggregateAwareTrait;
use Traya\EventInterface;
use Traya\EventPublisherInterface;

require '../vendor/autoload.php';

// Exception
class NotEnoughtMoneyException extends Exception
{
    public function __construct()
    {
        parent::__construct("Not enought money!");
    }
}

// Immutable state
class AccountState 
{
    /**
     * @var string
     */
    private $accountNumber;

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    private $withdraws;

    private $deposites;

    public function addWithdraw(DateTime $date, int $amount): void
    {
        $this->withdraws[date_format($date, 'Y-m-d H:i:s')] = $amount;
    }

    public function addDeposite(DateTime $date, int $amount): void
    {
        $this->deposites[date_format($date, 'Y-m-d H:i:s')] = $amount;
    }

    public function getBalance(): int
    {
        $withdrawSum = 0;
        $depositeSum = 0;

        foreach($this->withdraws as $key => $value) {
            $withdrawSum += $value;
        }

        foreach($this->deposites as $key => $value) {
            $depositeSum += $value;
        }


        return $depositeSum - $withdrawSum;
    }

    public function __construct(?string $accountNumber)
    {
        $this->accountNumber = $accountNumber;
        $this->withdraws = array();
        $this->deposites = array();
    }
}

// Events
class AbstractEvent implements EventInterface
{
    private $type;

    public function getType(): string
    {
        return $this->type;
    }

    protected $metadata;

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    protected $payload;

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function __construct(string $type)
    {
        $this->type = $type;
        $this->payload = array();
        $this->metadata = array();
    }

    protected function setAggregateId(string $aggregateId): void
    {
        $this->metadata['_aggregate_id'] = $aggregateId;
    }
    protected function setVersion(int $version): void
    {
        $this->metadata['_aggregate_version'] = $version;
    }

    public function getAggregateId(): string
    {
        return $this->metadata['_aggregate_id'];
    }
    public function getVersion(): int
    {
        return $this->metadata['_aggregate_version'];
    }
}

class Created extends AbstractEvent
{
    public function __construct(string $accountNumber)
    {
        parent::__construct("Created");
        $this->setAggregateId($accountNumber);
    }
}

class Withdrew extends AbstractEvent
{
    public function __construct(string $accountNumber, DateTime $date, int $amount)
    {
        parent::__construct("Withdrew");
        $this->payload['date'] = $date;
        $this->payload['amount'] = $amount;
        $this->setAggregateId($accountNumber);
    }

    public function getDate(): DateTime
    {
        return $this->payload['date'];
    }

    public function getAmount(): int
    {
        return $this->payload['amount'];
    }
}

class Deposited extends AbstractEvent
{
    public function __construct(string $accountNumber, DateTime $date, int $amount)
    {
        parent::__construct("Deposited");
        $this->payload['date'] = $date;
        $this->payload['amount'] = $amount;
        $this->setAggregateId($accountNumber);
    }

    public function getDate(): DateTime
    {
        return $this->payload['date'];
    }

    public function getAmount(): int
    {
        return $this->payload['amount'];
    }
}

// Aggregate
abstract class AccountAggregate extends AggregateRoot
{
    /**
     * @var AccountState
     */
    private $state;

    public function create(string $accountNumber)
    {
        $event = new Created($accountNumber);
        $this->record($event);
    }

    public function onCreated(Created $event)
    {
        $this->state = new AccountState($event->getAggregateId());
    }

    public function withraw(DateTime $date, int $amount)
    {
        //Check balance
        if($this->state->getBalance() > $amount) {
            $accountNumber = $this->state->getAccountNumber();
            $event = new Withdrew($accountNumber, $date, $amount);
            $this->record($event);
        } else {
            throw new NotEnoughtMoneyException();
        }
    }

    public function onWithdrew(Withdrew $event)
    {
        $this->state->addWithdraw($event->getDate(), $event->getAmount());
    }

    public function deposite(DateTime $date, int $amount)
    {
        $accountNumber = $this->state->getAccountNumber();
        $event = new Deposited($accountNumber, $date, $amount);
        $this->record($event);
    }

    public function onDeposited(Deposited $event)
    {
        $this->state->addDeposite($event->getDate(), $event->getAmount());
    }
}

// Publisher & Committer
class MemoryEventPublisher implements EventPublisherInterface {
    /**
     * @var EventInterface[]
     */
    private $events;

    public function __construct()
    {
        $this->events = array();
    }

    public function getEvents()
    {
        //Here it's better to have a dedicated repository to retrieve events and hydrage the aggregate
        // This accessor is here just for the example.
        return $this->events;
    }

    public function publish(EventInterface $event)
    {
        var_dump($event);
        array_push($this->events, $event);
    }
}

class EventCommitter 
{
    use CommitAggregateAwareTrait;

    /**
     * @var EventPublisherInterface
     */
    private $publisher;

    public function __construct(EventPublisherInterface $publisher)
    {
        $this->publisher = $publisher;
    }

    protected function getEventPublisher(): EventPublisherInterface {
        return $this->publisher;
    }
}

// Let's try everything :)
$publisher = new MemoryEventPublisher();
$committer = new EventCommitter($publisher);

$aggregate1 = new AccountAggregate();
$aggregate1->create(uniqid());
$committer->commit($aggregate1);

$aggregate2 = new AccountAggregate();
$aggregate2->load($publisher->getEvents());
var_dump($aggregate2);
$aggregate2->deposite(new DateTime(), 2000);
$committer->commit($aggregate2);

$aggregate3 = new AccountAggregate();
$aggregate3->load($publisher->getEvents());
var_dump($aggregate3);

try {
    $aggregate3->withraw(new DateTime(), 3000);
}
catch(Exception $ex) {
    if($ex instanceof NotEnoughtMoneyException) {
        var_dump("Not enought money");
    }
}

$aggregate3->withraw(new DateTime(), 500);
$committer->commit($aggregate3);


$aggregate4 = new AccountAggregate();
$aggregate4->load($publisher->getEvents());
var_dump($aggregate4);
$committer->commit($aggregate4);



