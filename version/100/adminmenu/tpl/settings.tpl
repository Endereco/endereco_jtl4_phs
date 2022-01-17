<form method="post">
    <input type="hidden" name="endsetid" value="{$endsetid}">
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Feld</th>
                    <th scope="col">Selektor</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <th scope="row">Rechnungsadresse - Festnetz - Eingabefeld</th>
                    <td>
                        <input type="text" class="form-control" name="rafix[s]" value="{$endjtlphssettings['rafix']['s']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rechnungsadresse - Festnetz - Wrapperfeld</th>
                    <td>
                        <input type="text" class="form-control" name="rafix[ws]" value="{$endjtlphssettings['rafix']['ws']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rechnungsadresse - Festnetz - REQ Feld</th>
                    <td>
                        <input type="text" class="form-control" name="rafix[rs]" value="{$endjtlphssettings['rafix']['rs']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rechnungsadresse - Festnetz - Landauswahl</th>
                    <td>
                        <input type="text" class="form-control" name="rafix[cs]" value="{$endjtlphssettings['rafix']['cs']|escape}">
                    </td>
                </tr>

                <tr>
                    <th scope="row">Rechnungsadresse - Mobil - Eingabefeld</th>
                    <td>
                        <input type="text" class="form-control" name="ramob[s]" value="{$endjtlphssettings['ramob']['s']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rechnungsadresse - Mobil - Wrapper</th>
                    <td>
                        <input type="text" class="form-control" name="ramob[ws]" value="{$endjtlphssettings['ramob']['ws']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rechnungsadresse - Mobil - REQ Feld</th>
                    <td>
                        <input type="text" class="form-control" name="ramob[rs]" value="{$endjtlphssettings['ramob']['rs']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Rechnungsadresse - Mobil - Landauswahl</th>
                    <td>
                        <input type="text" class="form-control" name="ramob[cs]" value="{$endjtlphssettings['ramob']['cs']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Festnetz - Eingabefeld</th>
                    <td>
                        <input type="text" class="form-control" name="lafix[s]" value="{$endjtlphssettings['lafix']['s']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Festnetz - Wrapper</th>
                    <td>
                        <input type="text" class="form-control" name="lafix[ws]" value="{$endjtlphssettings['lafix']['ws']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Festnetz - REQ Feld</th>
                    <td>
                        <input type="text" class="form-control" name="lafix[rs]" value="{$endjtlphssettings['lafix']['rs']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Festnetz - Landauswahl</th>
                    <td>
                        <input type="text" class="form-control" name="lafix[cs]" value="{$endjtlphssettings['lafix']['cs']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Mobil - Eingabefeld</th>
                    <td>
                        <input type="text" class="form-control" name="lamob[s]" value="{$endjtlphssettings['lamob']['s']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Mobil - Wrapper</th>
                    <td>
                        <input type="text" class="form-control" name="lamob[ws]" value="{$endjtlphssettings['lamob']['ws']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Mobil - REQ Feld</th>
                    <td>
                        <input type="text" class="form-control" name="lamob[rs]" value="{$endjtlphssettings['lamob']['rs']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Mobil - Landauswahl</th>
                    <td>
                        <input type="text" class="form-control" name="lamob[cs]" value="{$endjtlphssettings['lamob']['cs']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Lieferadresse - Toggle</th>
                    <td>
                        <input type="text" class="form-control" name="other[latog]" value="{$endjtlphssettings['other']['latog']|escape}">
                    </td>
                </tr>
                <tr>
                    <th scope="row">Pr&uuml;fen und Bestellen - Button</th>
                    <td>
                        <input type="text" class="form-control" name="other[ordsub]" value="{$endjtlphssettings['other']['ordsub']|escape}">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div>
        <button name="save-adv-settings" type="submit" value="Speichern" class="btn btn-primary"><i class="fa fa-save"></i> Speichern</button>
    </div>
</form>

