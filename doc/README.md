# Traya

## Getting started

An **AggregateRoot** is a cluster of domain objects that can be treated as a single unit (See [Martin Follwer post](https://martinfowler.com/bliki/DDD_Aggregate.html)). In Traya each aggregate needs to inherits from the **AggregateRoot** class and must contains domain representation and logic:

```php
class AccountAggregate extends AggregateRoot
{
    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @var int
     */
    private $balance;

    public function create(string $accountNumber)
    {
        //Do something
    }
}
```

The **AggregateRoot** abstract class have a couple of methods to deal with **Event**. In Traya, Event is a class that implements **[EventInterface](https://github.com/Wynd-Lab/traya/blob/master/src/EventInterface.php)**:

```php
// First you can create you own EventInterface implementations. Here it's a ValueObject that can be
// use in an Event Sourcing scenario with EventStore.
abstract class AbstractEvent implements EventInterface
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

// This event is a real one that can be use in an AggregateRoot
class Created extends AbstractEvent
{
    public function __construct(string $accountNumber)
    {
        parent::__construct("Created");
        $this->setAggregateId($accountNumber);
    }
}
```

> ⚠️ Use preterit verbs to name your events

Events can be use in an **AggregateRoot** to notify that something happen in the Domain. You can use the **record** method to do so:

```php
class AccountAggregate extends AggregateRoot
{
    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @var int
     */
    private $balance;

    function create(string $accountNumber)
    {
        $event = new Created($accountNumber);
        $this->record($event);
    }
}
```

When an Events are recorded, the **AggregateRoot** can be committed with the **CommitAggregateAwareTrait** trait. When a class use this trait, it needs to define how to retreive the **EventPublisherInterface** implementation. This interface define how to publish events.

> ⚠️ **EventPublisherInterface** need to be implement by yourself!

Here is an example with a memory **EventPubliserInterface** implementation:
```php
class MemoryEventPublisher implements EventPublisherInterface {
    /**
     * @var EventInterface[]
     */
    private $events;

    public function __construct()
    {
        $this->events = array();
    }

    public function publish(EventInterface $event)
    {
        var_dump($event);
        array_push($this->events, $event);
    }
}
```

> ⚠️ If you want to do Event Sourcing, you can implement the event publisher with [Prooph](https://github.com/prooph).

When a publisher is avaiable, you can create an event commiter by using the **CommitAggregateAwareTrait**:

```php
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
```

Now that everything is available, you can commit the **AggregateRoot**:

```php
$publisher = new MemoryEventPublisher();
$committer = new EventCommitter($publisher);

$aggregate1 = new AccountAggregate();
$aggregate1->create(uniqid());
$committer->commit($aggregate1);
```

When events are committed, you can load the **AggregateRoot** by hydrating it with the event collection. A **load** method is available on the class to do that:

```php
$aggregate2 = new AccountAggregate();
$aggregate2->load(array(
    new Created($accountNumber),
    // ...
));
```

**AggregateRoot** will automatically apply events and you have to handle them with methods that respect the following naming convention: **on{eventName}** (example for **Created** event you must have a **onCreate** method). Inside those methods you can add some logic for applying the event in the current aggregate instance:

```php
class AccountAggregate extends AggregateRoot
{
    /**
     * @var string
     */
    private $accountNumber;

    /**
     * @var int
     */
    private $balance;

    function create(string $accountNumber)
    {
        $event = new Created($accountNumber);
        $this->record($event);
    }

    function onCreated(Created $event)
    {
        $this->accountNumber = $event->accountNumber;
        $this->balance = 0;
    }
}
```

> ⚠️ **apply** is a protected method, so you can use it inside the **AggregateRoot** if needed.