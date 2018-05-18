
<h2>Créer partie</h2>

<h3>Nombre max de joueurs</h3>

<form action="/game/create" method="post">
    <input type="submit" value="Créer">
</form>

{if $newGameCode}
    <p>
        Le code de la partie <strong>{$newGameCode}</strong>
        <br/>

        <a href="/game/join/{$newGameCode}">Rejoindre la partie</a>

    </p>
{/if}