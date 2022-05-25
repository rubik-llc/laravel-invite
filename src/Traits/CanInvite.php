<?php

declare(strict_types=1);

namespace Rubik\LaravelInvite\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CanInvite
{
    /**
     * Defines polymorphic relation between the model that uses this trait and Invite
     * @return MorphMany
     */
    public function invites(): MorphMany
    {
        return $this->morphMany(config('invite.invite_model'), 'referable');
    }

//    public function invite($model, int $expiresAt){
//        Invite::referer($this)
//    }
//
//
//    /**
//     * Attach a comment to the specified model.
//     *
//     * @param $model
//     * @param string $comment
//     * @param bool $needsApproval
//     * @return false|Comment
//     */
//    public function commentTo($model, string $comment, bool $needsApproval = null): bool|Comment
//    {
//        $commentClass = config('comments.comment_model');
//        $comment = new $commentClass([
//            'comment' => $comment,
//            'commenter_id' => $this->getKey(),
//            'commenter_type' => get_class(),
//            'commentable_id' => $model->getKey(),
//            'commentable_type' => get_class($model),
//            'needs_approval' => $needsApproval ?? config('comments.needs_approval'),
//        ]);
//
//        return $this->comments()->save($comment);
//    }
//
//    /**
//     *
//     * The commenter_name attribute.
//     */
//    public function getCommenterNameAttribute()
//    {
//        return $this->getName();
//    }
//
//    /**
//     *
//     * Get the name of the commenter.
//     * @throws Exception
//     */
//    public function getName()
//    {
//        $nameAttribute = $this->nameAttribute ?? config('comments.commenter_name_attribute');
//
//        if (! isset($this->$nameAttribute) && ! config('comments.silence_name_attribute_exception')) {
//            throw new Exception("Attribute '{$nameAttribute}' does not exist in '".class_basename($this)."'.");
//        }
//
//        return $this->$nameAttribute;
//    }
}
