document.observe("dom:loaded", function() {
    const target = document.getElementById('checkout-shipping-method-load');

    if(target !== null) {
        const config = {
            attributes: false,
            attributeOldValue: false,
            characterData: false,
            characterDataOldValue: false,
            childList: true,
            subtree: true
        };

        function subscriber(mutations) {
            mutations.forEach((mutation) => {
                console.log(mutation);
                $$('input[type="radio"][name="shipping_method"]').each(function(el){
                    Event.observe(el, 'click', function(){
                        var getShippingCode = el.getValue();
                        $$('input.insShipping__checkbox').each(function(ell){
                            ell.checked = false;
                        });
                        $$('div.insShipping').each(function (ell) {
                            ell.addClassName('insShipping-hidden');
                        });

                        if (el.checked === true) {
                            if($('insShipping__' + getShippingCode)){
                                $('insShipping__' + getShippingCode).removeClassName('insShipping-hidden');
                            }

                            return false;
                        }
                    });
                });
            });
        }
        const observer = new MutationObserver(subscriber);

        observer.observe(target, config);

    }

    $$('.sp-methods input[type="radio"]').each(function (el) {
        Event.observe(el, 'click', function () {
            var getShippingCode = el.getValue();
            var addressId = el.readAttribute('address_id');
            $$('div.insShipping__' + addressId + ' input.insShipping__checkbox').each(function (ell) {
                ell.checked = false;
            });
            $$('div.insShipping__' + addressId).each(function (ell) {
                ell.addClassName('insShipping-hidden');
            });

            if (el.checked === true) {
                if ($('insShipping__' + getShippingCode + '__' + addressId)) {
                    $('insShipping__' + getShippingCode + '__' + addressId).removeClassName('insShipping-hidden');
                }
                return false;
            }
        });
    });

    $$('input[type="radio"][name="estimate_method"]').each(function(el){
        Event.observe(el, 'click', function(){
            var getShippingCode = el.getValue();
            $$('input.insShipping__checkbox').each(function(ell){
                ell.checked = false;
            });
            $$('div.insShipping').each(function (ell) {
                ell.addClassName('insShipping-hidden');
            });

            if (el.checked === true) {
                if($('insShipping__' + getShippingCode)){
                    $('insShipping__' + getShippingCode).removeClassName('insShipping-hidden');
                }
            }
        });
    });
});
