/**
 * Oggetto Web checkout extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto OnepageCheckout module to newer versions in the future.
 * If you wish to customize the Oggetto OnepageCheckout module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   design
 * @package    rwd_default
 * @copyright  Copyright (C) 2014 Oggetto Web ltd (http://oggettoweb.com/)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

function OpTotals (getUrl, $form) {
    var $totals = $j('#totals')
    ,   $grandTotal = $j('#grandTotal');

    this.setNew = function (data, callback) {
        if (data) {
            var oldTotal = $j('#grandTotal').attr('value');
            $totals.html($j(data).html());
            if ($grandTotal.attr('value') != oldTotal) {
                $grandTotal.trigger('change');
            }
        }
        Object.isFunction(callback) && callback();
    };

    this.get = function (success) {
        var that = this;

        $j.ajax({
            url: getUrl('getTotals', 'review'), // /checkout/review/getTotals',
            type: 'get',
            success: function (data) {
                that.setNew(data, $j.proxy(success, this, data));
            }
        });
    };
}

function OpReview (getUrl, $form) {
    var $btnContainer = $j('#placeOrderBtnContainer')
    ,   $reviewErrors = $j('#reviewErrors');

    this.canPlaceOrder = false;

    this.setNewButton = function (data) {
        $reviewErrors.empty();
        $btnContainer.html($j(data));
    };

    this.setMessage = function (data) {
        $reviewErrors.html($j(data));
    };

    this.getButton = function () {
        var that = this;

        $j.ajax({
            url: getUrl('getButton', 'review'), // '/checkout/review/getButton',
            type: 'get',
            success: function (data) {
                if (Object.isString(data)) {
                    data = JSON.parse(data);
                }
                if (data.status == 'success') {
                    that.setNewButton(data.message);
                    that.canPlaceOrder = true;
                } else {
                    that.setMessage(data.message);
                    that.canPlaceOrder = false;
                }
            }
        });
    };
}

function OpBilling (getUrl, $form) {
    var $billingErrors = $j('#billingErrors');

    function clearErrors () {
        $billingErrors.empty();
    }

    function setError (message) {
        if (message) {
            clearErrors();
            if (typeof(message) == 'string') {
                $billingErrors.append('<p>{msg}</p>'.replace('{msg}', message));
            } else {
                for (var i = 0; i < message.length; i++) {
                    $billingErrors.append('<p>{msg}</p>'.replace('{msg}', message[i]));
                }
            }
        }
    }

    this.save = function (success, showErrors) {
        showErrors = showErrors || true;

        $j.ajax({
            url: getUrl('save', 'billing'), // '/checkout/billing/save',
            type: 'post',
            data: $form.serialize(),
            beforeSend: function () {
                clearErrors();
            },
            success: function (data) {
                if (data) {
                    if (Object.isString(data)) {
                        data = JSON.parse(data);
                    }
                    if (data.status == 'success' && Object.isFunction(success)) {
                        success(data);
                    } else if (data.status == 'error') {
                        showErrors && setError(data.message);
                    }
                } else {
                    showErrors && setError('Server error.');
                }
            },
            error: function () {
                showErrors && setError('Server connection error.');
            }
        });
    };

    this.observeByFields = function (event, fields, callback) {
        if (typeof(fields) == 'string') {
            var el = $('billing:' + fields);
            $j(el).on(event, function () {
                callback(fields);
            });
        } else {
            for (var i = 0; i < fields.length; i++) {
                var el = $('billing:' + fields[i]);
                $j(el).on(event, function () {
                    callback(fields[i]);
                });
            }
        }
    };

    this.stopObservingByFields = function (event, fields) {
        for (var i = 0; i < fields.length; i++) {
            var el = $('billing:' + fields[i]);
            $j(el).off(event);
        }
    }
}

function OpShipping (getUrl, $form) {
    var $shippingErrors = $j('#shippingErrors');

    function clearErrors () {
        $shippingErrors.empty();
    }

    function setError (message) {
        if (message) {
            clearErrors();
            if (typeof(message) == 'string') {
                $shippingErrors.append('<p>{msg}</p>'.replace('{msg}', message));
            } else {
                for (var i = 0; i < message.length; i++) {
                    $shippingErrors.append('<p>{msg}</p>'.replace('{msg}', message[i]));
                }
            }
        }
    }

    var saveXhr = null;
    this.save = function (success, showErrors) {
        showErrors = showErrors || true;
        if (saveXhr != null) {
            saveXhr.abort();
        }
        saveXhr = $j.ajax({
            url: getUrl('save', 'shipping'), // '/checkout/shipping/save',
            type: 'post',
            data: $form.serialize(),
            beforeSend: function () {
                clearErrors();
            },
            success: function (data) {
                if (Object.isString(data)) {
                    data = JSON.parse(data);
                }
                if (data && data.status) {
                    if (data.status == 'success') {
                        Object.isFunction(success) && success(data);
                    } else {
                        showErrors && setError(data.message);
                    }
                } else {
                    showErrors && setError('Server error.');
                }
            },
            error: function () {
                showErrors && setError('Server connection error.');
            },
            complete: function () {
                saveXhr = null;
            }
        });
    };

    this.observeByFields = function (event, fields, callback) {
        for (var i = 0; i < fields.length; i++) {
            var el = $('shipping:' + fields[i]);
            $j(el).on(event, function () {
                callback(fields[i]);
            });
        }
    };

    var saveMethodXhr = null;
    this.saveMethod = function (success) {
        if (saveMethodXhr != null) {
            saveMethodXhr.abort();
        }
        saveMethodXhr = $j.ajax({
            url: getUrl('saveMethod', 'shipping'), // '/checkout/shipping/saveMethod',
            type: 'post',
            data: $form.serialize(),
            success: function (data) {
                if (Object.isString(data)) {
                    data = JSON.parse(data);
                }
                $j('#deliveryDateAndTime').remove();
                Object.isFunction(success) && success(data);
            },
            error: function () {
                setError('Server connection error.');
            },
            complete: function () {
                saveMethodXhr = null;
            }
        });
    };

    this.updateMethods = function (success) {
        var $shippingMethodLoad = $j('#checkout-shipping-method-load')
        ,   that = this;

        $j.ajax({
            url: getUrl('getMethods', 'shipping'), // '/checkout/shipping/getMethods',
            type: 'get',
            success: function (data) {
                $shippingMethodLoad.html(data);
                that.afterUpdateMethods(success);
            },
            error: function () {
                setError('Server connection error.');
            }
        });
    };

    this.afterUpdateMethods = function (saveSuccess) {
        var $shippingMethodLoad = $j('#checkout-shipping-method-load');
        if ($shippingMethodLoad.find('.sp-method').length == 1
            || $shippingMethodLoad.find('.sp-method:checked').length > 0) {
            this.saveMethod(saveSuccess);
        }
        if ($shippingMethodLoad.find('.sp-method').length >= 2) {
            var that = this;
            $j('.sp-method').on('change', function () {
                that.saveMethod(saveSuccess);
            });
        }
    };
}

function OpPayment (getUrl, $form) {
    var $paymentErrors = $j('#paymentErrors');

    var saveMethodXhr = null;
    this.saveMethod = function (success) {
        if (saveMethodXhr != null) {
            saveMethodXhr.abort();
        }
        saveMethodXhr = $j.ajax({
            url: getUrl('saveMethod', 'payment'), // '/checkout/payment/saveMethod',
            type: 'post',
            data: $form.serialize(),
            beforeSend: function () {
                $paymentErrors.empty();
            },
            success: function (data) {
                Object.isFunction(success) && success(data);
            },
            error: function () {
                $paymentErrors.html('Server connection error.');
            },
            complete: function () {
                saveMethodXhr = null;
            }
        });
    };

    var updateMethodsXhr = null;
    this.updateMethods = function (success) {
        var $paymentLoad = $j('#checkout-payment-method-load')
        ,   that = this;

        if (updateMethodsXhr != null) {
            updateMethodsXhr.abort();
        }
        updateMethodsXhr = $j.ajax({
            url: getUrl('getMethods', 'payment'), // '/checkout/payment/getMethods',
            type: 'get',
            success: function (data) {
                $paymentLoad.html(data);
                $j('.p-method').closest('dt').next('dd').find('input').addClass('p-method');
                that.afterUpdateMethods(success);
            },
            error: function () {
                $paymentErrors.html('Server connection error.');
            },
            complete: function () {
                updateMethodsXhr = null;
            }
        });
    };

    this.afterUpdateMethods = function (saveSuccess) {
        var $paymentLoad = $j('#checkout-payment-method-load')
        ,   that = this;

        if ($paymentLoad.find('.p-method').length == 1
            || $paymentLoad.find('.p-method:checked').length > 0) {
            this.saveMethod(saveSuccess);
        } else if ($paymentLoad.find('.p-method').length >= 2) {
            $j('.p-method').on('change', function () {
                that.saveMethod(saveSuccess);
            });
        }
    };
}

function OpCoupon (getUrl, $form) {
    var $couponMessage = $j('#couponMessage')
    ,   $couponForm = $j('#discount-coupon-form')
    ,   $addButton = $j('#couponAddButton')
    ,   $cancelButton = $j('#couponCancelButton');

    var applyXhr = null;
    this.applyCoupon = function (success) {
        if (applyXhr != null) {
            applyXhr.abort();
        }
        applyXhr = $j.ajax({
            url: $couponForm.data('action'),
            type: $couponForm.data('method'),
            data: $form.serialize(),
            beforeSend: function () {
                $addButton.addClass('hidden');
                if (!$couponMessage.hasClass('hidden')) {
                    $couponMessage
                        .empty()
                        .addClass('hidden');
                }
            },
            success: function (data) {
                if (Object.isString(data)) {
                    data = JSON.parse(data);
                }
                var message = data.message;
                if (data.status && data.status == 'success' && data.operation == 'apply' && data.coupon == 'valid') {
                    $j('#couponCancelButton').removeClass('hidden');
                    $j('#remove-coupone').attr('value', 1);

                    Object.isFunction(success) && success(data);
                } else {
                    $j('#couponAddButton').removeClass('hidden');
                    if (data.status && data.status == 'error' && !data.value) {
                        message = 'Something error';
                    }
                }
                $couponMessage
                    .removeClass('hidden')
                    .text(message);

            },
            error: function () {
                $couponMessage
                    .removeClass('hidden')
                    .text('Something goes wrong. Try once more');
            },
            complete: function () {
                applyXhr = null;
            }
        });
    };

    var cancelXhr = null;
    this.cancelCoupon = function (success) {
        if (cancelXhr != null) {
            cancelXhr.abort();
        }
        cancelXhr = $j.ajax({
            url: $couponForm.data('action'),
            type: $couponForm.data('method'),
            data: $form.serialize(),
            beforeSend: function () {
                $cancelButton.addClass('hidden');
                if (!$couponMessage.hasClass('hidden')) {
                    $couponMessage
                        .empty()
                        .addClass('hidden');
                }
            },
            success: function (data) {
                if (Object.isString(data)) {
                    data = JSON.parse(data);
                }
                var message = data.message;
                if (data.status && data.status == 'success' && data.operation == 'cancel') {
                    $addButton.removeClass('hidden');
                    $j('#remove-coupone').attr('value', 0);

                    Object.isFunction(success) && success(data);

                } else {
                    $cancelButton.removeClass('hidden');
                    if (data.status && data.status == 'error' && !data.value) {
                        message = 'Something error';
                    }
                }
                $j('#buttonsSet').removeClass('loader');
                $couponMessage
                    .removeClass('hidden')
                    .text(message);
            },
            error: function () {
                $couponMessage
                    .removeClass('hidden')
                    .text('Something goes wrong. Try once more');
            },
            complete: function () {
                cancelXhr = null;
            }
        });
    }
}

var paymentSelectors = []
,   totalsShippingSelectors = []
,   totalsPaymentSelectors = []
,   shippingSelectors = [];

$j(function () {
    var $form = $j('#checkoutForm');

    function getUrl (action, controller, frontName) {
        action = action || 'index';
        controller = controller || 'index';
        frontName = frontName || 'checkout';

        return '/' + frontName + '/' + controller + '/' + action;
    }

    var totals      = new OpTotals  (getUrl, $form)
    ,   review      = new OpReview  (getUrl, $form)
    ,   billing     = new OpBilling (getUrl, $form)
    ,   shipping    = new OpShipping(getUrl, $form)
    ,   payment     = new OpPayment (getUrl, $form)
    ,   coupon      = new OpCoupon  (getUrl, $form);

    var $checkoutForm = $j('#checkoutForm')
    ,   $shipToBilling = $j('#shipToBilling');

    if (window.billingRegionUpdater) {
        billingRegionUpdater.update();
    }

    function setNewDelivery (data) {
        if (Object.isString(data)) {
            data = JSON.parse(data);
        }
        data && data.delivery && $j('.block_shipping_method').append(data.delivery);
    }

    for (var i = 0; i < paymentAjaxReloadOn.length; i++) {
        var event = paymentAjaxReloadOn[i];
        if (event.id) {
            if (event.block == 'billing') {
                $j($(event.id)).on('change', function () {
                    billing.save($j.proxy(payment.updateMethods, payment));
                });
            } else if (event.block == 'totals') {
                paymentSelectors.push('#' + event.id);
            }
        }
    }
    for (var i = 0; i < paymentSelectors.length; i++) {
        $j(document).on('change', paymentSelectors[i], function () {
            payment.updateMethods();
        });
    }

    for (var i = 0; i < totalsAjaxReloadOn.length; i++) {
        var event = totalsAjaxReloadOn[i];
        if (event.class) {
            if (event.block == 'shipping') {
                totalsShippingSelectors.push('.' + event.class);
            } else if (event.block == 'payment') {
                totalsPaymentSelectors.push('.' + event.class);
            }
        } else if (event.id) {
            if (event.block == 'billing') {
                $j($(event.id)).on('change', function () {
                    billing.save($j.proxy(payment.updateMethods, payment,
                        $j.proxy(totals.get, totals,
                            $j.proxy(review.getButton, review)
                        )
                    ));
                });
            }
        }
    }
    for (var i = 0; i < totalsShippingSelectors.length; i++) {
        $j(document).on('change', totalsShippingSelectors[i], function () {
            shipping.afterUpdateMethods($j.proxy(totals.get, totals, $j.proxy(review.getButton, review)));
        });
    }
    for (var i = 0; i < totalsPaymentSelectors.length; i++) {
        $j(document).on('change', totalsPaymentSelectors[i], function () {
            payment.afterUpdateMethods($j.proxy(totals.get, totals, $j.proxy(review.getButton, review)));
        });
    }

    function observeShippingMethodsAjaxReloadOn () {
        shippingSelectors  = [];

        for (var i = 0; i < shippingMethodsAjaxReloadOn.length; i++) {
            var event = shippingMethodsAjaxReloadOn[i];
            if (event.id) {
                if (event.block == 'shipping') {
                    if ($shipToBilling.length && $shipToBilling.is(':checked')) {
                        var $elem = $j($(event.id.replace('shipping', 'billing')))
                        ,   $shippingElem = $j($(event.id));

                        if ($elem.length) {
                            $elem.on('change', function () {
                                billing.save(shipping.updateMethods(function (data) {
                                    setNewDelivery(data);
                                }));
                            });
                        }
                        if ($shippingElem.length) {
                            $shippingElem.off('change');
                        }
                    } else {
                        var $elem = $j($(event.id))
                        ,   $billingElem = $j($(event.id.replace('shipping', 'billing')));

                        if ($elem.length) {
                            $elem.on('change', function () {
                                shipping.save(shipping.updateMethods(function (data) {
                                    setNewDelivery(data);
                                }));
                            });
                        }
                        if ($billingElem.length) {
                            $billingElem.off('change');
                        }
                    }
                } else if (event.block == 'totals') {
                    shippingSelectors.push('#' + event.id);
                }
            }
        }
        for (var i = 0; i < shippingSelectors.length; i++) {
            $j(document).on('change', shippingSelectors[i], function () {
                shipping.updateMethods(function (data) {
                    setNewDelivery(data);
                });
            });
        }
    }

    observeShippingMethodsAjaxReloadOn();

    $checkoutForm.on('submit', function (e) {
        e.preventDefault();

        var $reviewErrors = $j('#reviewErrors')
        ,   $placeOrderButton = $j('#placeOrderBtnContainer').find('button');

        if (!review.canPlaceOrder) {
            return;
        }

        billing.save(function () {
            $j.ajax({
                url: getUrl('saveOrder', 'review'), // '/checkout/review/saveOrder',
                type: 'post',
                data: $form.serialize(),
                beforeSend: function () {
                    $placeOrderButton.addClass('hidden');
                    $reviewErrors.empty();
                },
                success: function (data) {
                    if (Object.isString(data)) {
                        data = JSON.parse(data);
                    }
                    if (data && data.status) {
                        if (data.status == 'success') {
                            $j('#onepageWrapper').html(data.message);
                        } else {
                            $reviewErrors.html(data.message);
                        }
                    }
                    $placeOrderButton.removeClass('hidden');
                }
            });
        });
    });

    if ($shipToBilling.length) {
        $shipToBilling.on('change', function () {
            if ($j(this).is(':checked')) {
                $j('#shippingBlock').addClass('hidden');

                billing.save(function () {
                    shipping.updateMethods(function (data) {
                        totals.setNew(data, $j.proxy(review.getButton, review));
                        setNewDelivery(data);
                    });
                });
            } else {
                billing.save();
                $j('#shippingBlock').removeClass('hidden');
            }
            observeShippingMethodsAjaxReloadOn();
        });
    }

    $j('#couponAddButton').on('click', function () {
        coupon.applyCoupon(function (data) {
            totals.setNew(data.totals, $j.proxy(review.getButton, review));
        });
    });

    $j('#couponCancelButton').on('click', function () {
        coupon.cancelCoupon(function (data) {
            totals.setNew(data, $j.proxy(review.getButton, review));
        });
    });

    var $subscribe = $j('#subscribe');
    if ($subscribe) {
        $subscribe.on('change', function () {
            $j.ajax({
                url: getUrl('subscribe'), // '/checkout/index/subscribe',
                data: $form.serialize(),
                type: 'post'
            });
        });
    }

    var $billingRegister = $j($('billing:register'));
    if ($billingRegister) {
        $billingRegister.on('change', function () {
            $j('#registerData').toggleClass('hidden');
        });
    }

    var $mainLoader = $j('#mainLoader');
    $j(document)
        .ajaxStart(function () {
            $mainLoader.removeClass('hidden');
        })
        .ajaxStop(function () {
            $mainLoader.addClass('hidden');
        });

    payment.afterUpdateMethods(
        $j.proxy(totals.get, totals,
            $j.proxy(review.getButton, review)
        )
    );

    shipping.afterUpdateMethods(function (data) {
        totals.setNew(data, $j.proxy(review.getButton, review));
        setNewDelivery(data);
    });
});