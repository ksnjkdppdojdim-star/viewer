<?php
namespace Core\Events\Events;

use Core\Events\Event;

class DatabaseEvent extends Event
{
    const BEFORE_CONNECT = 'db.before_connect';
    const CONNECTED      = 'db.connected';
    const CONNECT_ERROR  = 'db.connect_error';
    
    const BEFORE_QUERY   = 'db.before_query';
    const AFTER_QUERY    = 'db.after_query';
    const QUERY_ERROR    = 'db.query_error';
    
    const TRANSACTION_BEGIN    = 'db.transaction.begin';
    const TRANSACTION_COMMIT   = 'db.transaction.commit';
    const TRANSACTION_ROLLBACK = 'db.transaction.rollback';

    public function __construct(string $name, array $params = [])
    {
        parent::__construct($name, $params);
    }
    
    public function getSql(): ?string
    {
        return $this->getParam('sql');
    }
    
    public function getExecutionTime(): ?float
    {
        return $this->getParam('time');
    }
}
