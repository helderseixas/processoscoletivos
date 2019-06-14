<?php

namespace com\cminds\registration\model;

class CommentType extends Model {
	
	const COMMENT_TYPE = 'cmvl_user_note';
	
	protected $comment;
	
	public function __construct($comment) {
		$this->comment = (object)$comment;
	}
	
	
	public static function getInstance($commentId) {
		if ($comment = get_comment($commentId) AND $comment->comment_type == static::COMMENT_TYPE) {
			return new static($comment);
		}
	}
	
	
	public function save() {
		if ($id = $this->getId()) {
			return wp_update_comment((array)$this->comment);
		} else {
			$id = wp_insert_comment((array)$this->comment);
			if ($id) {
				$this->comment->comment_ID = $id;
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	public function getId() {
		return (isset($this->comment->comment_ID) ? $this->comment->comment_ID : null);
	}
	
	public function getAuthorId() {
		return $this->comment->user_id;
	}

	public function getContent() {
		return $this->comment->comment_content;
	}
	
	public function setContent($content) {
		$this->comment->comment_content = $content;
		return $this;
	}
	
	
	public function getDate() {
		return $this->comment->comment_date;
	}
	

	public function getPostId() {
		return $this->comment->comment_post_ID;
	}

	public function isApproved() {
		return ($this->comment->comment_approved == 1);
	}
	
	
	
}