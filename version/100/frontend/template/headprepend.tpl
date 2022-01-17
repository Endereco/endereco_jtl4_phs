<script>
  window.EnderecoPhone = {
    $globalFilters: {
      getRequiredClassName: []
    },
    $originalValues: {},
    deliverySeparated: {if $endtwoaddr} true {else} false {/if},
    $phoneCache: {},
    $pendingSubmit: false,
    $hasSubmitListener: false,
    $globalValues: {
      ioLink: '{$endriolink}'
    },
    $translations: {$endjtlphstranslations|@json_encode},
  };
  try {
    window.EnderecoPhone.countryConfig = {$endjtlphsconfig|@json_encode};
    window.EnderecoPhone.settings = {$endjtlphssettings|@json_encode};
    window.EnderecoPhone.$phoneCache = {$endjtlphsstatuses|@json_encode};
  } catch (e) {
    window.EnderecoPhone.countryConfig = {};
    window.EnderecoPhone.settings = {};
  }
</script>
<script async defer src="{$endphsscriptpath}"></script>
<style>
	.endereco-spin {
		-webkit-animation: spin 1s linear infinite;
		-moz-animation: spin 1s linear infinite;
		animation: spin 1s linear infinite;
	}
	@-moz-keyframes spin {
		100% { -moz-transform: rotate(360deg); }
	}
	@-webkit-keyframes spin {
		100% { -webkit-transform: rotate(360deg); }
	}
	@keyframes spin {
		100% {
			-webkit-transform: rotate(360deg);
			transform:rotate(360deg);
		}
	}
</style>
