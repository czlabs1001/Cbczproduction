(window.webpackWcBlocksJsonp=window.webpackWcBlocksJsonp||[]).push([[21],{471:function(e,t,c){"use strict";c.r(t);var s=c(0),n=c(41),r=c(5),o=c.n(r),a=c(1),i=c(309),l=c(45),m=c(11),p=c(153),u=c(7),b=c(3),E=c(376);c(375);var O=()=>{const{paymentMethods:e,isInitialized:t}=Object(i.a)(),{isCalculating:c,isProcessing:n,isAfterProcessing:r,isBeforeProcessing:o,isComplete:O,hasError:g}=Object(u.useSelect)(e=>{const t=e(b.CHECKOUT_STORE_KEY);return{isCalculating:t.isCalculating(),isProcessing:t.isProcessing(),isAfterProcessing:t.isAfterProcessing(),isBeforeProcessing:t.isBeforeProcessing(),isComplete:t.isComplete(),hasError:t.hasError()}}),j=Object(u.useSelect)(e=>e(b.PAYMENT_STORE_KEY).isExpressPaymentMethodActive());if(!t||t&&0===Object.keys(e).length)return null;const d=n||r||o||O&&!g;return Object(s.createElement)(s.Fragment,null,Object(s.createElement)(p.a,{isLoading:c||d||j},Object(s.createElement)("div",{className:"wc-block-components-express-payment wc-block-components-express-payment--cart"},Object(s.createElement)("div",{className:"wc-block-components-express-payment__content"},Object(s.createElement)(m.StoreNoticesContainer,{context:l.d.EXPRESS_PAYMENTS}),Object(s.createElement)(E.a,null)))),Object(s.createElement)("div",{className:"wc-block-components-express-payment-continue-rule wc-block-components-express-payment-continue-rule--cart"},Object(a.__)("Or","woo-gutenberg-products-block")))};t.default=e=>{let{className:t}=e;const{cartNeedsPayment:c}=Object(n.a)();return c?Object(s.createElement)("div",{className:o()("wc-block-cart__payment-options",t)},Object(s.createElement)(O,null)):null}},94:function(e,t,c){"use strict";var s=c(0);t.a=function(e){let{icon:t,size:c=24,...n}=e;return Object(s.cloneElement)(t,{width:c,height:c,...n})}}}]);