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

namespace N1ebieski\IDir\Http\Responses\Web\Dir;

use Illuminate\Http\Request;
use N1ebieski\IDir\Models\Dir;
use Illuminate\Http\RedirectResponse;
use N1ebieski\IDir\ValueObjects\Dir\Status;
use N1ebieski\IDir\ValueObjects\Price\Type;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Routing\UrlGenerator as URL;
use Illuminate\Contracts\Translation\Translator as Lang;
use N1ebieski\IDir\Http\Responses\Web\Dir\RedirectResponseFactory;

class UpdateRenewResponse implements RedirectResponseFactory
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param ResponseFactory $response
     * @param Config $config
     * @param Lang $lang
     * @param URL $url
     */
    public function __construct(
        protected Request $request,
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
        if (in_array($this->request->input('payment_type'), [Type::TRANSFER, Type::PAYPAL_EXPRESS])) {
            return $this->response->redirectToRoute('web.payment.dir.show', [
                $dir->payment->uuid,
                $dir->payment->driver
            ]);
        }

        return match ($dir->status->getValue()) {
            Status::ACTIVE => $this->response->redirectToRoute('web.profile.dirs')
                ->with('success', $this->lang->get('idir::dirs.success.update_renew.' . Status::ACTIVE)),

            default => $this->response->redirectToRoute('web.profile.dirs')
                ->with('success', $this->lang->get('idir::dirs.success.update_renew.' . Status::INACTIVE))
        };
    }
}
