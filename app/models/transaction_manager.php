<?php 
class TransactionManager extends AppModel {
    var $useTable = false;
 
    function begin() {
        return $this->getDataSource()->begin($this);
    }
    function commit() {
        return $this->getDataSource()->commit($this);
    }
    function rollback() {
        return $this->getDataSource()->rollback($this);
    }
}
?>