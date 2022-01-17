import axios from 'axios';

if (typeof window !== 'undefined') {
  /**
   * This function is called everytime there is a change, that can involve rerendering the form.
   *
   * @param event
   */
  window.calcEnrdPhs = (event = undefined) => {
    const $lafixActive = document.querySelector(window.EnderecoPhone.settings.lafix.s)
      && !document.querySelector(window.EnderecoPhone.settings.lafix.s).disabled;
    const $lamobActive = document.querySelector(window.EnderecoPhone.settings.lamob.s)
      && !document.querySelector(window.EnderecoPhone.settings.lamob.s).disabled;
    let isDeliveryVisible = false;
    const $rafixPhoneField = document.querySelector(window.EnderecoPhone.settings.rafix.s);
    const $ramobPhoneField = document.querySelector(window.EnderecoPhone.settings.ramob.s);
    const $lafixPhoneField = document.querySelector(window.EnderecoPhone.settings.lafix.s);
    const $lamobPhoneField = document.querySelector(window.EnderecoPhone.settings.lamob.s);

    if (event.type === 'DOMContentLoaded') {
      isDeliveryVisible = window.EnderecoPhone.deliverySeparated;
    } else {
      isDeliveryVisible = $lafixActive || $lamobActive;
    }

    if (isDeliveryVisible) {
      // Copy values to delivery.
      if ($lafixPhoneField.value === '' && ($rafixPhoneField.value !== '')) {
        $lafixPhoneField.value = $rafixPhoneField.value;
        $rafixPhoneField.value = '';
      }
      if ($lamobPhoneField.value === '' && $ramobPhoneField.value !== '') {
        $lamobPhoneField.value = $ramobPhoneField.value;
        $ramobPhoneField.value = '';
      }
    } else {
      // Copy values to invoice.
      if ($rafixPhoneField.value === '' && $lafixPhoneField.value !== '') {
        $rafixPhoneField.value = $lafixPhoneField.value;
        $lafixPhoneField.value = '';
      }
      if ($ramobPhoneField.value === '' && $lamobPhoneField.value !== '') {
        $ramobPhoneField.value = $lamobPhoneField.value;
        $lamobPhoneField.value = '';
      }
    }

    if (document.querySelectorAll('.endereco-phs-message')) {
      document.querySelectorAll('.endereco-phs-message').forEach((DOMElement) => {
        DOMElement.remove();
      });
    }

    // Set visibility
    ['rafix', 'lafix', 'ramob', 'lamob'].forEach((psetting) => {
      const $countrySelect = document.querySelector(window.EnderecoPhone.settings[psetting].cs);
      const $phoneField = document.querySelector(window.EnderecoPhone.settings[psetting].s);
      let requiredClassName = 'required';

      window.EnderecoPhone.$globalFilters.getRequiredClassName.forEach((filter) => {
        requiredClassName = filter(requiredClassName);
      });

      if (!!$countrySelect && !!$phoneField) {
        const $wrapperField = $phoneField.closest(window.EnderecoPhone.settings[psetting].ws);
        const $rqField = $phoneField.closest(window.EnderecoPhone.settings[psetting].rs);
        const $currentCountryValue = $countrySelect.value.toUpperCase();
        const $currentConfig = window.EnderecoPhone.countryConfig[$currentCountryValue];
        let shouldAlwaysHide = false;
        let messages = '';

        if (!window.EnderecoPhone.$originalValues[$wrapperField]) {
          window.EnderecoPhone.$originalValues[$wrapperField] = window.getComputedStyle($wrapperField).getPropertyValue('display');
        }

        if (psetting === 'rafix' || psetting === 'ramob') {
          shouldAlwaysHide = isDeliveryVisible;
        }

        if (psetting === 'rafix' || psetting === 'lafix') {
          if ($wrapperField) {
            if (!($currentConfig.fixed === 'X') && !shouldAlwaysHide) {
              $wrapperField.style.display = window.EnderecoPhone.$originalValues[$wrapperField];
            } else {
              $wrapperField.style.display = 'none';
            }
          }
          if ($rqField) {
            if ($currentConfig.fixed === 'R' && !shouldAlwaysHide) {
              $rqField.classList.add(requiredClassName);
              $phoneField.required = true;
            } else {
              $rqField.classList.remove(requiredClassName);
              $phoneField.required = false;
            }
          }
        }

        if (psetting === 'ramob' || psetting === 'lamob') {
          if ($wrapperField) {
            if (!($currentConfig.mobile === 'X') && !shouldAlwaysHide) {
              $wrapperField.style.display = window.EnderecoPhone.$originalValues[$wrapperField];
            } else {
              $wrapperField.style.display = 'none';
            }
          }
          if ($rqField) {
            if ($currentConfig.mobile === 'R' && !shouldAlwaysHide) {
              $rqField.classList.add(requiredClassName);
              $phoneField.required = true;
            } else {
              $rqField.classList.remove(requiredClassName);
              $phoneField.required = false;
            }
          }
        }

        if (!!window.EnderecoPhone.$phoneCache[$phoneField.value]
          && !window.EnderecoPhone.$phoneCache[$phoneField.value].isCorrect
        ) {
          window.EnderecoPhone.$phoneCache[$phoneField.value].errorMessages.forEach((message) => {
            messages += `<p><i class="fa fa-exclamation-triangle"></i> ${message}</p>`;
          });
          $phoneField.insertAdjacentHTML('afterend', `<div class="form-error-msg text-danger endereco-phs-message">${messages}</div>`);
          $rqField.classList.add('has-error');
        }

        if (!!window.EnderecoPhone.$phoneCache[$phoneField.value]
          && window.EnderecoPhone.$phoneCache[$phoneField.value].isCorrect
        ) {
          window.EnderecoPhone.$phoneCache[$phoneField.value].successMessages.forEach((message) => {
            messages += `<p><i class="fa fa-thumbs-up"></i> ${message}</p>`;
          });
          $phoneField.insertAdjacentHTML('afterend', `<div class="form-error-msg text-success endereco-phs-message">${messages}</div>`);
          $rqField.classList.add('has-success');
        }

        if (event.type === 'DOMContentLoaded') {
          $countrySelect.addEventListener('change', window.calcEnrdPhs);

          $phoneField.addEventListener('blur', (e) => {
            setTimeout(() => {
              if (!window.EnderecoPhone.$pendingSubmit) {
                const queryId = Math.random(1, 10000000000000);
                const number = e.target.value;
                const loadingMessage = window.EnderecoPhone
                  .$translations.endereco_jtl4_phs_phone_is_being_checked;
                if (!number) {
                  return;
                }
                $phoneField.insertAdjacentHTML(
                  'afterend',
                  `<div class="form-error-msg text-info endereco-phs-message" id="ednrm${queryId}"><i class="fa fa-spinner endereco-spin"></i> ${loadingMessage}</div>`,
                );
                axios.get(window.EnderecoPhone.$globalValues.ioLink, {
                  params: {
                    io: 'endereco_phs_request',
                    phoneNumber: number,
                    phoneType: e.target.name.includes('mobil') ? 'mobile' : 'fixed',
                    countryCode: $countrySelect.value,
                  },
                })
                  .then((response) => {
                    window.EnderecoPhone.$phoneCache[number] = response.data[number];
                    window.calcEnrdPhs(e);
                    if (document.getElementById(`ednrm${queryId}`)) {
                      document.getElementById(`ednrm${queryId}`).remove();
                    }
                  }, () => {
                    if (document.getElementById(`ednrm${queryId}`)) {
                      document.getElementById(`ednrm${queryId}`).remove();
                    }
                  });
              }
            }, 100);
          });

          if (!window.EnderecoPhone.$hasSubmitListener) {
            $phoneField.closest('form').addEventListener('submit', (e) => {
              if (!e.defaultPrevented) {
                window.EnderecoPhone.$pendingSubmit = true;
              }
            });
            window.EnderecoPhone.$hasSubmitListener = true;
          }
        }
      }
    });

    if (event.type === 'DOMContentLoaded' && document.querySelector(window.EnderecoPhone.settings.other.latog)) {
      document.querySelector(window.EnderecoPhone.settings.other.latog).addEventListener('change', (e) => {
        setTimeout(() => {
          window.calcEnrdPhs(e);
        }, 500);
      });
    }
  };

  document.addEventListener('DOMContentLoaded', window.calcEnrdPhs);
}
