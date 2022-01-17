<form method="post">
    <input type="hidden" name="confid" value="{$endconfid}">
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">L&auml;ndercode</th>
                    <th scope="col">Land</th>
                    <th scope="col">Mobil</th>
                    <th scope="col">Festnetz</th>
                    <th scope="col">Format <small>(erweiterte Funktionen)</small></th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$endjtlphsconfig item=actitem key=actkey}
                    <tr>
                        <th scope="row">{$actkey}</th>
                        <td>
                            {$actitem['name']}
                        </td>
                        <td>
                            <select class="form-control" name="country[{$actkey}][mobile]">
                                <option value="X" {if 'X' == $actitem['mobile']}selected{/if}>Verstecken</option>
                                <option value="O" {if 'O' == $actitem['mobile']}selected{/if}>Anzeigen, optional</option>
                                <option value="R" {if 'R' == $actitem['mobile']}selected{/if}>Anzeigen, Pflicht</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="country[{$actkey}][fixed]">
                                <option value="X" {if 'X' == $actitem['fixed']}selected{/if}>Verstecken</option>
                                <option value="O" {if 'O' == $actitem['fixed']}selected{/if}>Anzeigen, optional</option>
                                <option value="R" {if 'R' == $actitem['fixed']}selected{/if}>Anzeigen, Pflicht</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" name="country[{$actkey}][format]">
                                <option value="" {if '' == $actitem['format']}selected{/if}>Alles zulassen</option>
                                <option value="E164" {if 'E164' == $actitem['format']}selected{/if}>E.164</option>
                                <option value="INTERNATIONAL" {if 'INTERNATIONAL' == $actitem['format']}selected{/if}>International</option>
                                <option value="NATIONAL" {if 'NATIONAL' == $actitem['format']}selected{/if}>National</option>
                            </select>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <button name="save-countries" type="submit" value="Speichern" class="btn btn-primary"><i class="fa fa-save"></i> Speichern</button>
    </div>
</form>

