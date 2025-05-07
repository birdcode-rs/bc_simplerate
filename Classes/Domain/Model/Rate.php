<?php

/***
 *
 * This file is part of the "BC Simplerate" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2025 Bird Dev <bird.dev@birdcode.in.rs>, Bird Code
 *
 ***/

namespace BirdCode\BcSimplerate\Domain\Model;
 
/**
 * Rate.
 */
class Rate extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * rate.
     *
     * @var string
     */
    protected $rate = '';

    /**
     * recordid.
     *
     * @var int
     */
    protected $recordid;
    
     /**
     * tablename.
     *
     * @var string
     */
    protected $tablename = '';
 
    /**
     * note.
     *
     * @var string
     */
    protected $note = '';
 
    /**
     * note.
     *
     * @var string
     */
    protected $roundrate = '';

    /**
     * feuser.
     *
     * @var ?int
     */
    protected $feuser = null;
     
     /**
     * get rate.
     *
     * @return string $rate
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * set rate.
     *
     * @param string $rate
     *
     * @return void
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }
  
    /**
     * get recordid.
     *
     * @return int $recordid
     */
    public function getRecordid()
    {
        return $this->recordid;
    }

    /**
     * set recordid.
     *
     * @param int $recordid
     *
     * @return void
     */
    public function setRecordid($recordid)
    {
        $this->recordid = $recordid;
    }

    /**
     * get tablename.
     *
     * @return string $tablename
     */
    public function getTablename()
    {
        return $this->tablename;
    }

    /**
     * set tablename.
     *
     * @param string $tablename
     *
     * @return void
     */
    public function setTablename($tablename)
    {
        $this->tablename = $tablename;
    }

    /**
     * get note.
     *
     * @return string $note
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * set note.
     *
     * @param string $note
     *
     * @return void
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * Get the value of feuser.
     *
     * @return int|null
     */
    public function getFeuser(): ?int
    {
        return $this->feuser;
    }

    /**
     * Set the value of feuser.
     *
     * @param int|null $feuser
     * @return void
     */
    public function setFeuser(?int $feuser): void
    {
        $this->feuser = $feuser;
    }
}