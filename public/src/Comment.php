<?php

namespace SitePoint;

use medoo\medoo;

class Comment
{
    protected $database;

    protected $name;
    protected $email;
    protected $comment;
    protected $submissionDate;

    /**
     * Comment constructor.
     * @param medoo $medoo
     */
    public function __construct(medoo $medoo)
    {
        $this->database = $medoo;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        $collection = [];
        $comments = $this->database->select('comments', '*', ["ORDER" => "comments.submissionDate DESC"]);
        if ($comments) {
            foreach ($comments as $array) {
                $comment = new self($this->database);
                $collection[] = $comment
                    ->setComment($array['comment'])
                    ->setEmail($array['email'])
                    ->setName($array['name'])
                    ->setSubmissionDate($array['submissionDate']);
            }
        }
        return $collection;
    }

    /**
     * @param mixed $comment
     * @return Comment
     */
    public function setComment($comment)
    {
        if (strlen($comment) < 10) {
            throw new \InvalidArgumentException('Comment too short!');
        } else {
            $this->comment = $comment;
        }
        return $this;
    }

    /**
     * @param $name
     * @return Comment
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * @param $email
     * @return $this
     */
    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        } else {
            throw new \InvalidArgumentException('Not a valid email!');
        }
        return $this;
    }

    /**
     * @param $date
     * @return $this
     */
    protected function setSubmissionDate($date)
    {
        $this->submissionDate = $date;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return mixed
     */
    public function getSubmissionDate()
    {
        return $this->submissionDate;
    }

    /**
     * @return bool|\PDOStatement
     * @throws \Exception
     */
    public function save()
    {
        if ($this->getName() && $this->getEmail() && $this->getComment()) {
            $this->setSubmissionDate(date('Y-m-d H:i:s'));
            return $this->database->insert('comments', [
                'name' => $this->getName(),
                'email' => $this->getEmail(),
                'comment' => $this->getComment(),
                'submissionDate' => $this->getSubmissionDate()
            ]);
        }
        throw new \Exception("Failed to save!");
    }
}