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

namespace N1ebieski\IDir\Http\Responses\Admin\Dir;

use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\IDir\Http\Responses\Admin\Dir\RedirectResponseFactory;

class Store3Response implements RedirectResponseFactory
{
    /**
     * Undocumented function
     *
     * @param ResponseFactory $response
     * @param Config $config
     * @param Lang $lang
     * @param URL $url
     */
    public function __construct(
        protected ResponseFactory $response,
        protected Config $config,
        protected Lang $lang,
        protected URL $url
    ) {
        //
    }

    /**
     * Undocumented function
     *
     * @param Dir $dir
     * @return RedirectResponse
     */
    public function makeResponse(Dir $dir): RedirectResponse
    {
        /** @var int */
        $status = $dir->status->getValue();

        return match ($status) {
            Status::INACTIVE => $this->response->redirectToRoute('admin.dir.index')
                ->with('success', $this->lang->get('idir::dirs.success.store.' . Status::INACTIVE)),

            Status::ACTIVE => $this->response->redirectToRoute('admin.dir.index')
                ->with('success', $this->lang->get('idir::dirs.success.store.' . Status::ACTIVE)),

            Status::PAYMENT_INACTIVE => $this->response->redirectToRoute('admin.payment.dir.show', [
                    $dir->payment->uuid,
                    $dir->payment->driver
                ]),

            default => throw new \Exception("No response was found for the status '{$status}'")
        };
    }
}
