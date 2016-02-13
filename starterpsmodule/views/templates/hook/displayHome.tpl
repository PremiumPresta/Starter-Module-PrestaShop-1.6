{*
* Starter Module
* 
*  @author    PremiumPresta <office@premiumpresta.com>
*  @copyright PremiumPresta
*  @license   http://creativecommons.org/licenses/by/4.0/ CC BY 4.0
*}

<div id="starterpsmodule" class="block">
  <p class="title_block">{l s='Starter PrestaShop Module' mod='starterpsmodule'}</p>
  <div class="block_content">
    <blockquote>
      {$quote[$cart->id_lang]|escape:'htmlall':'UTF-8'}
      {if $show_author}
        <span>{$author|escape:'htmlall':'UTF-8'}</span>
      {/if}
    </blockquote>
  </div>
</div>