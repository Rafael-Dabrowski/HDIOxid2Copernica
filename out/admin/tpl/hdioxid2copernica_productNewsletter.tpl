[{oxid_include_dynamic file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign}]

<div id="hdio2c">
    <h1>Product Newsletter</h1>
    <p>Sie können hier Produkte auswählen welche Sie in einem Newsletter einbinden wollen. </p>

    <h2>1. Kampagne bennenen</h2>
    <p>Bennen Sie die Kampagne aussagekräftig, anhand dessen können Sie in Copernica eine Miniselction anlegen um durch die Produkte dieser Kampagne iterieren zu können</p>
    <input data-bind="value:name" type="text" /><span class="wait" data-bind="visible: waiting().name"></span><span data-bind="visible:nameExists">Eine Kampagne mit dem Namen existiert bereits.</span>
    <h2>2. Produkte Auswählen</h2>
    <p>Wählen Sie die Produkte aus welche beworben werden sollen. Einfach mit dem namen oder der Artikel nummeranfangen zu suchen und auf hinzufügen clicken. Zum entfernen einfach in der Auswahlliste darauf clicken</p>
    <div style="float: left; width: 200px; ">
        Suche: <br/>
        <input type="text" data-bind="value: product, valueUpdate: 'afterkeydown'" /><span data-bind="visible: waiting().products" class="wait"></span> (<span data-bind="text:foundProducts"></span>)
        <br/>
        <select style="float:left; " data-bind="options: selectedProducts, optionsText: 'name', value: selectedProduct" multiple size="20"></select>
    </div>
    <div>
        <div style="margin:0px auto 5px" class="paging"></div>
        <div style="height: 400px;overflow: auto;"  data-bind="foreach: products">
            <div style="min-height: 100px; width: 300px;margin:6px; float: left; border: 1px solid #ccc; padding: 10px;  " class="clearfix product">
                <img data-bind="attr:{src: thumbnail}" style="float:left;">
                <b data-bind="text:name"> </b> <br />
                Artikelnummer: <span data-bind="text:artnum"></span><br/>
                Preis: <span data-bind="html:price"></span><br/>
                <p data-bind="html: description"></p>
                <button data-bind="click: $parent.addProduct, enable: $parent.containsProduct">hinzufügen</button>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>

    <h2>3. Informationen übermitteln</h2>
    <button data-bind="enable: name() != '' && selectedProducts().length > 0 && !saved() && !nameExists() && !waiting().name(), click: save">Absenden</button>
    <div style="background: green; color: white; padding: 10px; " data-bind="visible: name() != '' && selectedProducts().length > 0">
        <p>Nach dem Speichern können Sie auf die Produkte in Ihren Templates wie Folgt zugreifen: </p>
        <pre >
{loadprofile source="[{$newsletterConfig->name}]" multiple="true" assign=profiles [{$newsletterConfig->fieldMatching.Campaign->name}]="<span data-bind="text:name"></span>"}
{foreach from=$profiles item=profile}
    {$profile.[{$newsletterConfig->fieldMatching.Campaign->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.OXID->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.Name->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.SKU->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.Description->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.Price->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.VAT->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.Thumbnail->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.Icon->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.Picture->name}]}
    {$profile.[{$newsletterConfig->fieldMatching.URL->name}]}
{/foreach}
        </pre>
    </div>

    <!--<h3>Debug</h3>
    <pre data-bind="text:ko.toJSON(ViewModel, null, 2)"></pre>-->
    <script type="text/javascript">
        var ajax_url = '[{$ajax_url}]';
        var ViewModel = function () {
            self= this;
            self.name= ko.observable();
            self.nameExists= ko.observable();
            self.waiting= ko.observable({
                products: ko.observable(false),
                name: ko.observable(false)
            });
            self.product= ko.observable("");
            self.foundProducts= ko.observable(0);
            self.pageNr= ko.observable();
            self.products= ko.observableArray([]);
            self.selectedProducts= ko.observableArray([]);
            self.selectedProduct= ko.observable();
            self.saved= ko.observable(false);
            self.containsProduct= function(item) {
                if (ko.utils.arrayFirst(self.selectedProducts(), function(old) {
                    return (item.id == old.id);
                }) == undefined) {
                    return false;
                } else {
                    return true;
                }
            };
            self.addProduct= function(item)
            {
                if (ko.utils.arrayFirst(self.selectedProducts(), function(old) {
                    return (item.id == old.id);
                }) == undefined) {
                    self.selectedProducts.push(item);
                } else {
                    alert('bereits enthalten');
                }
            };
            self.removeProduct = function(item) {
                self.selectedProducts.remove(item);
            };
            self.save= function()
            {
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    dataType: "json",
                    data: {
                        scope: 'oxid',
                        func: "saveNlCampaign",
                        config: ko.toJSON(self)
                    },
                    success: function(data) {
                        //TODO: needs to be implemented
                        alert("gespeichert");
                        self.saved(true);
                    },
                    error: function(data) {
                        alert('Hmm fehler q(o_Ó)p')
                       
                    }
                });
            };
            self.findProducts = function(queryString, pageNr) {
                if (!queryString) {
                    self.products([]);
                    self.foundProducts(0);
                    $(".paging").html("");
                    return;
                }
                self.waiting().products(true);
                $.ajax({
                    type:"POST",
                    url: ajax_url,
                    dataType:"json",
                    data: {
                        scope: 'oxid',
                        func: "findProducts",
                        query: queryString,
                        pgNr: pageNr
                    },
                    success: function(data) {
                        if (data.result === true) {
                            self.products(data.products);
                            self.foundProducts(data.found);
                            paging(data.pageSize, data.found, pageNr);
                        }
                        var maxHeight = 0;
                        $('.product').each(function() {
                            if (maxHeight < $(this).outerHeight())
                            {
                                maxHeight = $(this).outerHeight();
                            }
                        });
                        $('.product').height(maxHeight);
                        self.waiting().products(false);
                    },
                    error: function(data) {
                        alert('Hmm fehler q(o_Ó)p')
                        self.waiting().products(false);
                    }
                });
            }; //findproducts
            
        //removing selected Product when click in Selectbox;
        self.selectedProduct.subscribe(function(item) {
            self.selectedProducts.remove(item);
        });
  
         //observs changing of product Inputfield with a delay of 500ms
        ko.computed(function() {
            self.findProducts(this.product(), 0);
        },self).extend({throttle: 500});

        //check if Name Exists
        ko.computed(function() {
            if (!self.name())
            {
                self.nameExists(false);
                return;
            }
            self.waiting().name(true);
            $.ajax({
                type:"POST",
                url: ajax_url,
                dataType:"json",
                data: {
                    func: "database_searchProfiles",
                    params: {
                        id: [{$newsletterConfig->id}]                        
                    },
                    aStruct: {
                        requirements: [
                                {
                            fieldname: '[{$newsletterConfig->fieldMatching.Campaign->name}]',
                                                                                    operator:'=',
                            value: self.name()
                        }
                        ]}
                },
                success: function(data) {
                    if (data.length > 0) {
                        self.nameExists(true);
                    } else {
                        self.nameExists(false);
                    }
                    self.waiting().name(false);
                },
                error: function(data) {
                    alert('Hmm fehler q(o_Ó)p')
                    self.waiting().products(false);
                }
            });
        }, self); //computed: name change  
        
        
        
            
        }; //ViewModel

        ko.applyBindings(new ViewModel());

        //creates Paging
        function paging(size, max, page) {
            if (size && max) {
                mod = max % size;
                pages = (max - mod) / size;
                if (mod > 0)
                    pages++;
                $(".paging").html(function() {
                    html = "";
                    for (i = 1; i <= pages; i++)
                    {
                        var curPage = "";
                        if ((i - 1) == page) {
                            curPage = "curPage";
                        }
                        html += '&laquo;<span class="page ' + curPage + '">' + i + '</span>&raquo; ';
                    }
                    return html;
                });
            }
        }

        $().ready(function() {
            $('.paging').on('click', '.page', function() {
                findProducts(ViewModel.product(), $(this).text() - 1);
            });
        });

    </script>
</div>
[{include file="bottomitem.tpl"}]