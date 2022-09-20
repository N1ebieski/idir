<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is licenced under the Software License Agreement
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://intelekt.net.pl/pages/regulamin
 *
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * @author    Mariusz Wysokiński <kontakt@intelekt.net.pl>
 * @copyright Since 2019 INTELEKT - Usługi Komputerowe Mariusz Wysokiński
 * @license   https://intelekt.net.pl/pages/regulamin
 */

namespace N1ebieski\IDir\Models\Comment\Dir;

use Illuminate\Database\Eloquent\Casts\Attribute;
use N1ebieski\ICore\Models\Comment\Comment as BaseComment;

/**
 * N1ebieski\IDir\Models\Comment\Dir\Comment
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $model_id
 * @property string $model_type
 * @property int $parent_id
 * @property string $content_html
 * @property string $content
 * @property \N1ebieski\ICore\ValueObjects\Comment\Status $status
 * @property \N1ebieski\ICore\ValueObjects\Comment\Censored $censored
 * @property int $position
 * @property int $real_depth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Franzose\ClosureTable\Extensions\Collection|BaseComment[] $ancestors
 * @property-read int|null $ancestors_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|Comment[] $children
 * @property-read int|null $children_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|BaseComment[] $childrens
 * @property-read int|null $childrens_count
 * @property-read \Franzose\ClosureTable\Extensions\Collection|BaseComment[] $descendants
 * @property-read int|null $descendants_count
 * @property-read string $content_as_html
 * @property-read string $created_at_diff
 * @property-read string $poli
 * @property-read string $poli_self
 * @property-read string $type
 * @property-read string $updated_at_diff
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $morph
 * @property-read Comment|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Rating\Comment\Rating[] $ratings
 * @property-read int|null $ratings_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\N1ebieski\ICore\Models\Report\Comment\Report[] $reports
 * @property-read int|null $reports_count
 * @property-read \N1ebieski\ICore\Models\User|null $user
 * @method static Builder|Comment active()
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] all($columns = ['*'])
 * @method static Builder|Entity ancestors()
 * @method static Builder|Entity ancestorsOf($id)
 * @method static Builder|Entity ancestorsWithSelf()
 * @method static Builder|Entity ancestorsWithSelfOf($id)
 * @method static Builder|Entity childAt($position)
 * @method static Builder|Entity childNode()
 * @method static Builder|Entity childNodeOf($id)
 * @method static Builder|Entity childOf($id, $position)
 * @method static Builder|Entity childrenRange($from, $to = null)
 * @method static Builder|Entity childrenRangeOf($id, $from, $to = null)
 * @method static Builder|Entity descendants()
 * @method static Builder|Entity descendantsOf($id)
 * @method static Builder|Entity descendantsWithSelf()
 * @method static Builder|Entity descendantsWithSelfOf($id)
 * @method static \N1ebieski\ICore\Database\Factories\Comment\CommentFactory factory(...$parameters)
 * @method static Builder|Comment filterAuthor(?\N1ebieski\ICore\Models\User $author = null)
 * @method static Builder|Comment filterCategory(?\N1ebieski\ICore\Models\Category\Category $category = null)
 * @method static Builder|Comment filterCensored(?int $censored = null)
 * @method static Builder|Comment filterCommentsOrderBy(?string $orderby = null)
 * @method static Builder|Comment filterExcept(?array $except = null)
 * @method static Builder|Comment filterOrderBy(?string $orderby = null)
 * @method static Builder|Comment filterOrderBySearch(?string $search = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator filterPaginate(?int $paginate = null)
 * @method static Builder|Comment filterReport(?int $report = null)
 * @method static Builder|Comment filterSearch(?string $search = null)
 * @method static Builder|Comment filterStatus(?int $status = null)
 * @method static Builder|Entity firstChild()
 * @method static Builder|Entity firstChildOf($id)
 * @method static Builder|Entity firstSibling()
 * @method static Builder|Entity firstSiblingOf($id)
 * @method static \Franzose\ClosureTable\Extensions\Collection|static[] get($columns = ['*'])
 * @method static Builder|Comment inactive()
 * @method static Builder|Entity lastChild()
 * @method static Builder|Entity lastChildOf($id)
 * @method static Builder|Entity lastSibling()
 * @method static Builder|Entity lastSiblingOf($id)
 * @method static Builder|Entity neighbors()
 * @method static Builder|Entity neighborsOf($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment newQuery()
 * @method static Builder|Entity nextSibling()
 * @method static Builder|Entity nextSiblingOf($id)
 * @method static Builder|Entity nextSiblings()
 * @method static Builder|Entity nextSiblingsOf($id)
 * @method static Builder|Comment orderBySearch(string $term)
 * @method static Builder|Comment poli()
 * @method static Builder|Comment poliType()
 * @method static Builder|Entity prevSibling()
 * @method static Builder|Entity prevSiblingOf($id)
 * @method static Builder|Entity prevSiblings()
 * @method static Builder|Entity prevSiblingsOf($id)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment query()
 * @method static Builder|Comment root()
 * @method static Builder|Comment search(string $term)
 * @method static Builder|Comment sibling()
 * @method static Builder|Entity siblingAt($position)
 * @method static Builder|Entity siblingOf($id)
 * @method static Builder|Entity siblingOfAt($id, $position)
 * @method static Builder|Entity siblings()
 * @method static Builder|Entity siblingsOf($id)
 * @method static Builder|Entity siblingsRange($from, $to = null)
 * @method static Builder|Entity siblingsRangeOf($id, $from, $to = null)
 * @method static Builder|Comment uncensored()
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCensored($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereContentHtml($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereModelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereRealDepth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Comment whereUserId($value)
 * @method static Builder|Comment withAllRels(?string $orderby = null)
 * @method static Builder|Comment withSumRating()
 * @mixin \Eloquent
 */
class Comment extends BaseComment
{
    // Configurations

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass()
    {
        return \N1ebieski\ICore\Models\Comment\Comment::class;
    }

    // Accessors

    /**
     *
     * @return Attribute
     */
    public function poli(): Attribute
    {
        return new Attribute(fn (): string => 'dir');
    }

    /**
     *
     * @return Attribute
     */
    public function modelType(): Attribute
    {
        return new Attribute(fn (): string => \N1ebieski\IDir\Models\Dir::class);
    }
}
