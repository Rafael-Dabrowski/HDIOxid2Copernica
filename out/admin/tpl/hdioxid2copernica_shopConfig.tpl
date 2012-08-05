[{oxid_include_dynamic file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<h1>Konfiguration: Shop</h1>
<style type="text/css">

.odd {background: #ddd}
</style>

<h3>Einstellungen für stehengelassene Warenkörbe</h3>
Benachrichtigungen versenden: <br />
<input data-bind="checked: basketInfoType" name="basketInfoType" type="radio" value="interval" id="basketInterval" checked/><label for="basketInterval">in einem festen Intervall </label><br />
<input data-bind="checked: basketInfoType" name="basketInfoType" type="radio" value="specific" id="basketSpecific"/><label for="basketSpecific">Zeitpunkt nach stehenlassen des Warenkorbes</label> <br />

<div>
    <span data-bind="visible: basketInfoType() == 'interval'">Alle</span><span data-bind="if: basketInfoType() == 'specific'">Nach</span> <input type="text" data-bind="value: intervalValue, valueUpdate: 'afterkeydown'"/><select data-bind="options:intervalOptions, optionsText:'display', value: intervalOption"></select><br />
    <input data-bind="checked: basketSpecificTime" type="checkbox" value="basketSpecificTime" id="basketSpecificTime" /> <label for="basketSpecificTime">Zu einer bestimmten Uhrzeit</label>
    <div data-bind="visible: basketSpecificTime() == true">
        <input type="text" data-bind="value: basketHour" size="5" />:<input size="5" data-bind="value: basketMinute" type="text" />
    </div>
</div>

<h3>Synchronization</h3>
<button data-bind="click:initialSync">Create Initial SyncItems</button><button data-bind="click:doSync">Do Sync</button><button data-bind="click:flushDb">FlushDB</button>
<table>
    <thead><th></th><th>Kunde</th><th>Vorgang</th></thead>
    <tbody data-bind="template:{name: 'toSync-template', foreach:toSync}"></tbody>

</table>

<script type="text/html" id="toSync-template">
    <tr class="odd" data-bind="css:{odd: (($index() %2) == 1)}">
        <td data-bind="text:$index"></td>
        <!-- <td data-bind="text:name"></td>-->
        <td data-bind="text:operation"></td>
        <td data-bind="html:object"></td>
        <td data-bind="text:date"></td>
    </tr>
    </script>
<!--
<h3>Debug</h3>
<pre data-bind="text:ko.toJSON(ViewModel, null, 2)"></pre> -->
<script type="text/javascript">
var ajax_url = '[{$ajax_url}]';
    var ViewModel = {
    basketInfoType: ko.observable("interval"), 
    intervalOptions: [
        {display: "tage(n)", value:"daily"},
        {display: "wochen", value:"weekly"}, 
        {display: "monate(n)", value:"monthly"}
    ], 
    intervalOption: ko.observable(),
    intervalValue: ko.observable(),
    basketSpecificTime: ko.observable(), 
    basketHour:ko.observable("12"), 
    basketMinute: ko.observable("00"), 
    toSync: ko.observableArray() , 
    initialSync: function(){
    $.ajax({
            url: ajax_url,
            type:'POST', 
            dataType:'json',
            data: {
                func: 'initialSync', 
                scope: 'oxid'
            },
            success: function(data){
                 console.log(data);
            },
            error: function(data){
             
                alert("[{oxmultilang ident='HDIO2C_ERROR_BADNEWS' }]");
            }
        });
    },
    flushDb: function(){    $.ajax({
            url: ajax_url,
            type:'POST', 
            dataType:'json',
            data: {
                func: 'flushDb', 
                scope: 'oxid'
            },
            success: function(data){
             
            },
            error: function(data){
             
                alert("[{oxmultilang ident='HDIO2C_ERROR_BADNEWS' }]");
            }
        });
        }, 
        doSync: function() {
          $.ajax({
            url: ajax_url,
            type:'POST', 
            dataType:'json',
            data: {
                func: 'doSync', 
                scope: 'oxid'
            },
            success: function(data){
             console.log(data);
            },
            error: function(data){
             
                alert("Sync fehler");
            }
        });
        }
   
}

ViewModel.intervalValue.subscribe(function (item){
    ViewModel.intervalValue(getNumber(item)); 
});

ViewModel.basketHour.subscribe(function (item){
    ViewModel.basketHour(getNumber(item)); 
});

ViewModel.basketMinute.subscribe(function (item){
    ViewModel.basketMinute(getNumber(item)); 
});

function getNumber(text)
{
    var value = parseInt(text); 
    if(isNaN(value)){value = null; }
    return value;
}

function loadSyncData()
{

$.ajax({
            url: ajax_url,
            type:'POST', 
            dataType:'json',
            data: {
                func: 'getSyncInfo', 
                scope: 'oxid'
            },
            success: function(data){
                if(data !== false)
                {
                    ViewModel.toSync(data.objects);
                }else{
                    alert("SyncinfoFehler");
 
                }
            
            },
            error: function(data){
             
                alert("[{oxmultilang ident='HDIO2C_ERROR_BADNEWS' }]");
            }
        });
   
    }
  

ko.applyBindings(ViewModel);

setInterval(function(){loadSyncData()}, 2000
    
);


</script>

[{include file="bottomitem.tpl"}]