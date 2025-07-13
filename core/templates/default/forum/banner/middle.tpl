{if $is_mobile neq 'mobile'}

    {php}

    {/php}
    {assign var=x value= 1|rand:2}
    {if $x == 1}
        {*if $bannerNGS*}

        </div>
    {else}
        <div id="middle_brn" class="box_brn">

        </div>
    {/if}

{/if}