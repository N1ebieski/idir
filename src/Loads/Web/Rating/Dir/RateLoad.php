<?php

namespace N1ebieski\IDir\Loads\Web\Rating\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use N1ebieski\IDir\Models\Rating\Dir\Rating;

class RateLoad
{
    /**
     *
     * @var Rating
     */
    protected $rating;

    /**
     *
     * @param Request $request
     * @param Rating $rating
     * @return void
     */
    public function __construct(Request $request, Rating $rating)
    {
        /**
         * @var Dir
         */
        $dir = $request->route('dir');

        $this->rating = $dir->makeRepo()->firstRatingByUser($request->user())
            ?? $rating->setRelations(['morph' => $dir]);
    }

    /**
     * Get the value of rating
     *
     * @return  Rating
     */
    public function getRating(): Rating
    {
        return $this->rating;
    }
}
