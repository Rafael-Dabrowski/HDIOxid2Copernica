

Alle Inhalte eine subcollection laden/ incl. miniselection:

{foreach from=$profile.Basket item=sub}
                {in_miniselection miniselection=5 }
                               {$sub.id}<br />
                {/in_miniselection}
{/foreach}
