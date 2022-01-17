<form method="post">
    <input type="hidden" name="endfncid" value="{$endfncid}">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Basisfunktion <small>(kostenlos)</small></h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <div class="checkbox disabled">
                    <label>
                        <input type="checkbox" disabled checked> Eingabe der Telefonnummer je Land konfigurieren
                    </label>
                </div>
            </div>
        </div>
    </div>


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Erweiterte Funktionen <small>(kostenpflichtig)</small></h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="useAdvancedFunctions" {if $endjtlphsfunctions['useAdvancedFunctions']} checked {/if}> Die Telefonnummer auf Format und G&uuml;ltigkeit gegen die Schnittstelle von Endereco pr&uuml;fen.
                    </label>
                </div>
            </div>

            <div class="alert alert-info">
                <p>Diese Funktionen nutzen die <a href="https://github.com/Endereco/enderecoservice_api" target="_blank">API</a> von <a href="https://www.endereco.de" target="_blank">Endereco</a>. Nutzung der API ist nur mit einem g&uuml;ltigen API Key m&ouml;glich.</p>
                <p>Alle Einstellungen gelten nur f&uuml;r die Nummern in der Lieferadresse bzw. der Rechnugsadresse, wenn diese mit der Lieferadresse identisch ist.</p>
            </div>

            <div class="form-group clear-both">
                <label>Endereco API Key</label>
                <input type="text" class="form-control" name="apiKey" placeholder="Endereco API Key hier eintragen" value="{$endjtlphsfunctions['apiKey']|escape}">
                <small>Hast du noch keinen? <a href="https://www.endereco.de/jtl/" target="_black">Hier anfordern</a></small>
            </div>

            <div class="form-group clear-both">
                <label>
                    Wenn die Telefonnummer falsch ist
                </label>
                <select class="form-control" name="wrongNumberAction">
                    <option value="1" {if 1 == $endjtlphsfunctions['wrongNumberAction']} selected {/if}>Nichts unternehmen</option>
                    <option value="2" {if 2 == $endjtlphsfunctions['wrongNumberAction']} selected {/if}>Die Bestellung blockieren und eine Fehlermeldung anzeigen, wenn die Telefonnummer im Pflichtfeld steht</option>
                    <option value="3" {if 3 == $endjtlphsfunctions['wrongNumberAction']} selected {/if}>Die Bestellung immer blockieren und eine Fehlermeldung anzeigen</option>
                </select>
            </div>

            <div class="form-group clear-both">
                <label>
                    Wenn die Telefonnummer im falschen Format ist
                </label>
                <select class="form-control" name="wrongFormatAction">
                    <option value="1" {if 1 == $endjtlphsfunctions['wrongFormatAction']} selected {/if}>Nichts unternehmen</option>
                    <option value="2" {if 2 == $endjtlphsfunctions['wrongFormatAction']} selected {/if}>Die Bestellung blockieren und eine Fehlermeldung anzeigen, wenn die Telefonnummer im Pflichtfeld steht</option>
                    <option value="3" {if 3 == $endjtlphsfunctions['wrongFormatAction']} selected {/if}>Die Bestellung immer blockieren und eine Fehlermeldung anzeigen</option>
                    <option value="4" {if 4 == $endjtlphsfunctions['wrongFormatAction']} selected {/if}>Das Format automatisch entsprechend der L&auml;nderkonfiguration korrigieren</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    Wenn die Mobilfunknetz- oder Festnetznummern im falschen Telefonfeld steht
                </label>
                <select class="form-control" name="wrongTypeAction">
                    <option value="1" {if 1 == $endjtlphsfunctions['wrongTypeAction']} selected {/if}>Nichts unternehmen</option>
                    <option value="2" {if 2 == $endjtlphsfunctions['wrongTypeAction']} selected {/if}>Mobilfunk- oder Festnetznummer ins richtige Feld verschieben, wenn das richtige Feld leer ist.</option>
                    <option value="3" {if 3 == $endjtlphsfunctions['wrongTypeAction']} selected {/if}>Mobilfunk- oder Festnetznummer immer ins richtige Feld verschieben, das Feld wird ggf. &uuml;berschrieben.</option>
                </select>
            </div>

            <div class="form-group">
                <label>
                    Nach wie vielen Tagen soll eine bereits gepr&uuml;fte Nummer erneut gepr&uuml;ft werden?
                </label>
                <select class="form-control" name="invalidateAfterDays">
                    <option value="1" {if 1 == $endjtlphsfunctions['invalidateAfterDays']} selected {/if}>1 (Tag)</option>
                    <option value="7" {if 7 == $endjtlphsfunctions['invalidateAfterDays']} selected {/if}>7 (Woche)</option>
                    <option value="30" {if 30 == $endjtlphsfunctions['invalidateAfterDays']} selected {/if}>30 (Monat)</option>
                    <option value="90" {if 90 == $endjtlphsfunctions['invalidateAfterDays']} selected {/if}>90 (Quartal)</option>
                </select>
            </div>
        </div>
    </div>

    <div>
        <button name="save-settings" type="submit" value="Speichern" class="btn btn-primary"><i class="fa fa-save"></i> Speichern</button>
    </div>
</form>

