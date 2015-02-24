<?php

namespace Morus\FasBundle\Classes;

use Symfony\Component\Translation\Translator;

/**
 * Statement Import Result Log
 *
 * @author Michael
 */
class ImportLog{
    
    const EMPTY_NULL_VALUE = 'error.import.empty_null_value';
    const INVALID_DATETIME_VALUE = 'error.import.invalid_datetime_value';
    const INVALID_DATE_VALUE = 'error.import.invalid_date_value';
    const INVALID_TIME_VALUE = 'error.import.invalid_time_value';
    
    /**
     * @var Translator
     */
    private $translator;
    
    /**
     * @var string
     */
    private $row;
    
    /**
     * @var string
     */
    private $column;
    
    /**
     * @var string
     */
    private $message;
    
    /**
     * Constructor
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Get log
     *
     * @return integer 
     */
    public function getLog()
    {
        return $this->translator->trans($this->message, array(
            '%row' => $this->row,
            '%column' => $this->column,
                ));
    }
    
    /**
     * Set log
     *
     * @param integer $row, integer $column, string $message
     * @return ImportLog
     */
    public function setLog($row, $column, $message)
    {
        $this->row = $row;
        $this->column = $column;
        $this->message = $message;
        
        return $this;
    }
}
