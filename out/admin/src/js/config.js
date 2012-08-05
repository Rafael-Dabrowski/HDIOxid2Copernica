//URL for Ajax calls
var ajax_url = '[{$ajax_url}]';

//DataModel for Configuration
var ConfigModel = {
    curStep: ko.observable("Account"),
    host: ko.observable("[{$host}]"),
    user: ko.observable("[{$user}]"),
    password: ko.observable("[{$pass}]"),
    account: ko.observable("[{$acc}]"),
    //Observables for Waiting cursor
    waiting:ko.observable({
        database: ko.observable(false),
        collections:ko.observable(false),
        loading: ko.observable(false)
    }),
    databases: ko.observableArray([]),
    loginStatus: ko.observable(""),
    loginOK: ko.observable(false),
    //Database and Colelction Configuration
    database: ko.observable({
        collections: ko.observableArray([]),
        //Selected Database + FieldMatching
        account: ko.observable(
                {
            collection: ko.observable(),
            fields: ko.observableArray([]),
            name:"account",
            waiting: ko.observable(false),
            fieldMatching: ko.observableArray([
                    {internal:"OXID", name: "[{oxmultilang ident='HDIO2C_account_OXID' }]", type: "text", newField: "OXID", match: ko.observable(), collection: "account" },
                    {internal:"Firstname", name: "[{oxmultilang ident='HDIO2C_account_Firstname' }]", type: "text", newField: "Firstname", match: ko.observable(), collection: "account" },
                    {internal:"Lastname", name: "[{oxmultilang ident='HDIO2C_account_Lastname' }]", type: "text", newField: "Lastname", match: ko.observable(), collection: "account" },
                    {internal:"Salutation", name: "[{oxmultilang ident='HDIO2C_account_Salutation' }]", type: "text", newField: "Salutation", match: ko.observable(), collection: "account" },
                    {internal:"Email", name: "[{oxmultilang ident='HDIO2C_account_Email' }]", type: "Email", newField: "Email", match: ko.observable(), collection: "account" },
                    {internal:"Newsletter", name: "[{oxmultilang ident='HDIO2C_account_Newsletter' }]", type: "text", newField: "Newsletter", match: ko.observable(), collection: "account" },
                    {internal:"Group", name: "[{oxmultilang ident='HDIO2C_account_Group' }]", type: "text", newField: "Group", match: ko.observable(), collection: "account" },
                    {internal:"CustomerId", name: "[{oxmultilang ident='HDIO2C_account_CustomerID' }]", type: "text", newField: "Custummor_ID", match: ko.observable(), collection: "account" },
                    {internal:"Birthday", name: "[{oxmultilang ident='HDIO2C_account_Birthday' }]", type: "Date", newField: "Birthday", match: ko.observable(), collection: "account" },
                    {internal:"Registered", name: "[{oxmultilang ident='HDIO2C_account_Registered' }]", type: "Date", newField: "Registered", match: ko.observable(), collection: "account" },
                    {internal:"Bonus", name: "[{oxmultilang ident='HDIO2C_account_Bonus' }]", type: "float", newField: "Bonus", match: ko.observable(), collection: "account" }
            ])
        }),
        orders: ko.observable(
                {
            collection: ko.observable(),
            name:"orders",
            newCollectionName:"Orders",
            waiting: ko.observable(false),
            fields: ko.observableArray([]),
            fieldMatching: ko.observableArray([
                    {internal:"OXID", name: "[{oxmultilang ident='HDIO2C_order_OXID' }]", type: "text", newField: "OXID", match: ko.observable(), collection: "orders" },
                    {internal:"OrderNumber", name: "[{oxmultilang ident='HDIO2C_order_OrderNr' }]", type: "text", newField: "Order_Number", match: ko.observable(), collection: "orders" },
                    {internal:"OrderDate", name: "[{oxmultilang ident='HDIO2C_order_OrderDate' }]", type: "date", newField: "Order_Date", match: ko.observable(), collection: "orders" },
                    {internal:"PaymentType", name: "[{oxmultilang ident='HDIO2C_order_PaymentType' }]", type: "text", newField: "Payment_Type", match: ko.observable(), collection: "orders" },
                    {internal:"TotalBrutto", name: "[{oxmultilang ident='HDIO2C_order_TotalBrutto' }]", type: "float", newField: "Order_Total_Brutto", match: ko.observable(), collection: "orders" },
                    {internal:"TotalNetto", name: "[{oxmultilang ident='HDIO2C_order_TotalNetto' }]", type: "float", newField: "Order_Total_Netto", match: ko.observable(), collection: "orders" },
                    {internal:"ShippingCost", name: "[{oxmultilang ident='HDIO2C_order_Shipping' }]", type: "float", newField: "Shipping", match: ko.observable(), collection: "orders" },
                    {internal:"VAT", name: "[{oxmultilang ident='HDIO2C_order_VAT' }]", type: "float", newField: "VAT", match: ko.observable(), collection: "orders" },
                    {internal:"Discount", name: "[{oxmultilang ident='HDIO2C_order_Discount' }]", type: "float", newField: "Discount", match: ko.observable(), collection: "orders" },
                    {internal:"Currency", name: "[{oxmultilang ident='HDIO2C_order_Currency' }]", type: "text", newField: "Currency", match: ko.observable(), collection: "orders" },
                    {internal:"TotalWeight", name: "[{oxmultilang ident='HDIO2C_order_TotalWeight' }]", type: "text", newField: "Total_Weight", match: ko.observable(), collection: "orders" },
                    {internal:"ShippingType", name: "[{oxmultilang ident='HDIO2C_order_ShippingType' }]", type: "text", newField: "Shipping_Type", match: ko.observable(), collection: "orders" }
            ])
        }),
        basket: ko.observable(
                {
            collection: ko.observable(),
            name: "basket",
            newCollectionName: "Basket",
            waiting: ko.observable(false),
            fields: ko.observableArray([]),
            fieldMatching: ko.observableArray([

                    {internal:"OXID", name: "[{oxmultilang ident='HDIO2C_basket_OXID' }]", type: "text", newField: "", match: ko.observable(), collection: "basket" },
                    {internal:"SKU", name: "[{oxmultilang ident='HDIO2C_basket_Articlenumber' }]", type: "text", newField: "SKU", match: ko.observable(), collection: "basket" },
                    {internal:"Name", name: "[{oxmultilang ident='HDIO2C_basket_Name' }]", type: "text", newField: "Name", match: ko.observable(), collection: "basket" },
                    {internal:"Description", name: "[{oxmultilang ident='HDIO2C_basket_Description' }]", type: "big", newField: "Description", match: ko.observable(), collection: "basket" },
                    {internal:"Price", name: "[{oxmultilang ident='HDIO2C_basket_Price' }]", type: "float", newField: "Price", match: ko.observable(), collection: "basket" },
                    {internal:"TotalPrice", name: "[{oxmultilang ident='HDIO2C_basket_TotalPrice' }]", type: "float", newField: "Total_Price", match: ko.observable(), collection: "basket" },
                    {internal:"VAT", name: "[{oxmultilang ident='HDIO2C_basket_Vat' }]", type: "float", newField: "Vat", match: ko.observable(), collection: "basket" },
                    {internal:"Thumbnail", name: "[{oxmultilang ident='HDIO2C_basket_Thumbnail' }]", type: "text", newField: "Thumbnail", match: ko.observable(), collection: "basket" },
                    {internal:"Icon", name: "[{oxmultilang ident='HDIO2C_basket_Icon' }]", type: "text", newField: "Icon", match: ko.observable(), collection: "basket" },
                    {internal:"Picture", name: "[{oxmultilang ident='HDIO2C_basket_Picture' }]", type: "text", newField: "Picture", match: ko.observable(), collection: "basket", oxField:"" },
                    {internal:"Variant", name: "[{oxmultilang ident='HDIO2C_basket_Variant' }]", type: "text", newField: "Variant", match: ko.observable(), collection: "basket" },
                    {internal:"URL", name: "[{oxmultilang ident='HDIO2C_basket_URL' }]", type: "text", newField: "URL", match: ko.observable(), collection: "basket" },
                    {internal:"Quantity", name: "[{oxmultilang ident='HDIO2C_basket_Quantity' }]", type: "float", newField: "Quantity", match: ko.observable(), collection: "basket" },
                    {internal:"PersonalizedParameter", name: "[{oxmultilang ident='HDIO2C_basket_PersonalizedParameter' }]", type: "text", newField: "Personalized_Parameter", match: ko.observable(), collection: "basket" }

            ])
        }),
        orderItems: ko.observable(
                {
            collection: ko.observable(),
            name: "orderItems",
            newCollectionName: "OrderItems",
            waiting: ko.observable(false),
            fields: ko.observableArray([]),
            fieldMatching: ko.observableArray([

                    {internal:"OXID", name: "[{oxmultilang ident='HDIO2C_orderItems_OXID' }]", type: "text", newField: "OXID", match: ko.observable(), collection: "orderItems" },
                    {internal:"SKU", name: "[{oxmultilang ident='HDIO2C_orderItems_Articlenumber' }]", type: "text", newField: "SKU", match: ko.observable(), collection: "orderItems" },
                    {internal:"Name", name: "[{oxmultilang ident='HDIO2C_orderItems_Name' }]", type: "text", newField: "Name", match: ko.observable(), collection: "orderItems" },
                    {internal:"Description", name: "[{oxmultilang ident='HDIO2C_orderItems_Description' }]", type: "big", newField: "Description", match: ko.observable(), collection: "orderItems" },
                    {internal:"Price", name: "[{oxmultilang ident='HDIO2C_orderItems_Price' }]", type: "float", newField: "Price", match: ko.observable(), collection: "orderItems" },
                    {internal:"TotalPrice", name: "[{oxmultilang ident='HDIO2C_orderItems_TotalPrice' }]", type: "float", newField: "Total_Price", match: ko.observable(), collection: "orderItems" },
                    {internal:"VAT", name: "[{oxmultilang ident='HDIO2C_orderItems_Vat' }]", type: "float", newField: "Vat", match: ko.observable(), collection: "orderItems" },
                    {internal:"Thumbnail", name: "[{oxmultilang ident='HDIO2C_orderItems_Thumbnail' }]", type: "text", newField: "Thumbnail", match: ko.observable(), collection: "orderItems" },
                    {internal:"Icon", name: "[{oxmultilang ident='HDIO2C_orderItems_Icon' }]", type: "text", newField: "Icon", match: ko.observable(), collection: "orderItems" },
                    {internal:"Picture", name: "[{oxmultilang ident='HDIO2C_orderItems_Picture' }]", type: "text", newField: "Picture", match: ko.observable(), collection: "orderItems", oxField:"" },
                    {internal:"Variant", name: "[{oxmultilang ident='HDIO2C_orderItems_Variant' }]", type: "text", newField: "Variant", match: ko.observable(), collection: "orderItems" },
                    {internal:"URL", name: "[{oxmultilang ident='HDIO2C_orderItems_URL' }]", type: "text", newField: "URL", match: ko.observable(), collection: "orderItems" },
                    {internal:"Quantity", name: "[{oxmultilang ident='HDIO2C_orderItems_Quantity' }]", type: "float", newField: "Quantity", match: ko.observable(), collection: "orderItems" },
                    {internal:"PersonalizedParameter", name: "[{oxmultilang ident='HDIO2C_orderItems_PersonalizedParameter' }]", type: "text", newField: "Personalized_Parameter", match: ko.observable(), collection: "orderItems" }

            ])
        }),
        addresses: ko.observable(
                {
            collection: ko.observable(),
            name: "addresses",
            newCollectionName: "Addresses",
            waiting: ko.observable(false),
            fields: ko.observableArray(),
            fieldMatching: ko.observableArray([
                    {internal:"OXID", name: "[{oxmultilang ident='HDIO2C_addresses_OXID' }]", type: "text", newField: "OXID", match: ko.observable(), collection: "addresses" },
                    {internal:"Firstname", name: "[{oxmultilang ident='HDIO2C_addresses_Firstname' }]", type: "text", newField: "Firstname", match: ko.observable(), collection: "addresses" },
                    {internal:"Lastname", name: "[{oxmultilang ident='HDIO2C_addresses_Lastname' }]", type: "text", newField: "Lastname", match: ko.observable(), collection: "addresses" },
                    {internal:"Street", name: "[{oxmultilang ident='HDIO2C_addresses_Street' }]", type: "text", newField: "Street", match: ko.observable(), collection: "addresses" },
                    {internal:"Number", name: "[{oxmultilang ident='HDIO2C_addresses_Number' }]", type: "text", newField: "Number", match: ko.observable(), collection: "addresses" },
                    {internal:"Zipcode", name: "[{oxmultilang ident='HDIO2C_addresses_Zip' }]", type: "text", newField: "Zip", match: ko.observable(), collection: "addresses" },
                    {internal:"City", name: "[{oxmultilang ident='HDIO2C_addresses_City' }]", type: "text", newField: "City", match: ko.observable(), collection: "addresses" },
                    {internal:"Country", name: "[{oxmultilang ident='HDIO2C_addresses_Country' }]", type: "text", newField: "Country", match: ko.observable(), collection: "addresses" },
                    {internal:"Telephone", name: "[{oxmultilang ident='HDIO2C_addresses_Phone' }]", type: "text", newField: "Phone", match: ko.observable(), collection: "addresses" },
                    {internal:"Fax", name: "[{oxmultilang ident='HDIO2C_addresses_Fax' }]", type: "text", newField: "Fax", match: ko.observable(), collection: "addresses" },
                    {internal:"Mobile", name: "[{oxmultilang ident='HDIO2C_addresses_Mobile' }]", type: "text", newField: "Mobile", match: ko.observable(), collection: "addresses" },
                    {internal:"Company", name: "[{oxmultilang ident='HDIO2C_addresses_Company' }]", type: "text", newField: "Company", match: ko.observable(), collection: "addresses" }

            ])
        }),
        products: ko.observable(
                {
            collection: ko.observable(),
            name: "products",
            newCollectionName: "NewsletterProducts",
            waiting: ko.observable(false),
            fields: ko.observableArray([]),
            fieldMatching: ko.observableArray([
                    {internal:"Campaign", name: "[{oxmultilang ident='HDIO2C_products_Campaign' }]", type: "text", newField: "Campaign", match: ko.observable(), collection: "products" },
                    {internal:"OXID", name: "[{oxmultilang ident='HDIO2C_products_OXID' }]", type: "text", newField: "OXID", match: ko.observable(), collection: "products" },
                    {internal:"SKU", name: "[{oxmultilang ident='HDIO2C_products_Articlenumber' }]", type: "text", newField: "SKU", match: ko.observable(), collection: "products" },
                    {internal:"Name", name: "[{oxmultilang ident='HDIO2C_products_Name' }]", type: "text", newField: "Name", match: ko.observable(), collection: "products" },
                    {internal:"Description", name: "[{oxmultilang ident='HDIO2C_products_Description' }]", type: "big", newField: "Description", match: ko.observable(), collection: "products" },
                    {internal:"Price", name: "[{oxmultilang ident='HDIO2C_products_Price' }]", type: "float", newField: "Price", match: ko.observable(), collection: "products" },
                    {internal:"VAT", name: "[{oxmultilang ident='HDIO2C_products_Vat' }]", type: "float", newField: "Vat", match: ko.observable(), collection: "products" },
                    {internal:"Thumbnail", name: "[{oxmultilang ident='HDIO2C_products_Thumbnail' }]", type: "text", newField: "Thumbnail", match: ko.observable(), collection: "products" },
                    {internal:"Icon", name: "[{oxmultilang ident='HDIO2C_products_Icon' }]", type: "text", newField: "Icon", match: ko.observable(), collection: "products" },
                    {internal:"Picture", name: "[{oxmultilang ident='HDIO2C_products_Picture' }]", type: "text", newField: "Picture", match: ko.observable(), collection: "products", oxField:"" },
                    {internal:"Variant", name: "[{oxmultilang ident='HDIO2C_products_Variant' }]", type: "text", newField: "Variant", match: ko.observable(), collection: "products" },
                    {internal:"URL", name: "[{oxmultilang ident='HDIO2C_products_URL' }]", type: "text", newField: "URL", match: ko.observable(), collection: "products" }
            ])
        })
    }),
    //creates a newField for selected Database or Collection
    newField: function(collection)
    {
        var func = "collection_createField";
        if (collection.collection == "account" || collection.collection == "products")
        {
            func = "database_createField";
        }
        ConfigModel.database()[collection.collection]().waiting(true);
        var params = {
            id: ConfigModel.database()[collection.collection]().collection().id,
            name: collection.newField,
            type: collection.type
        };

        $.ajax({
            url: ajax_url,
            type:'POST',
            dataType:'json',
            data: {
                func: func,
                params:{

                }
            },
            success: function(data) {
                if (data !== false)
                {
                    ConfigModel.database()[collection.collection]().fields.push(data);
                    collection.match(data);
                } else {
                    alert("[{oxmultilang ident='HDIO2C_ERROR_SEEMEDOKBUTSTILLWRONG' }]");
                    console.log(data);
                }
                ConfigModel.database()[collection.collection]().waiting(false);
            },
            error: function(data) {
                ConfigModel.database()[collection.collection]().waiting(false);
                alert("[{oxmultilang ident='HDIO2C_ERROR_BADNEWS' }]");
            }
        });
    },
    //name of Database to Create
    newDatabase: ko.observable(""),
    //Check if to display new Collection Form
    checkNewCollection: function(collection) {
        if (ConfigModel.database()[collection]().collection()) {
            if (ConfigModel.database()[collection]().collection().id == -1) {
                return true;
            }
        }
        return false;
    },
    //Creates a Database on Server
    createDatabase: function() {
        $.ajax({
            type:"POST",
            url: ajax_url,
            dataType:"json",
            data: {
                func: "Account_createDatabase",
                params: {
                    name: this.newDatabase()
                }
            },
            success: function(data) {

                if (data.result === true)
                {
                    ConfigModel.loginStatus("[{ oxmultilang ident='HDIO2C_CONNECTION_CONFIRMED'}]");
                    ConfigModel.loginOK(true);
                    getDatabases();
                } else {
                    ConfigModel.loginStatus(data.result);
                    ConfigModel.databases([]);
                }
            },
            error: function(data) {
                ConfigModel.loginStatus("[{ oxmultilang ident='HDIO2C_BAD_URL'}]");
                ConfigModel.databases([]);
            }
        });
    },
    //Creates a Collection
    createCollection: function(collection) {
        collection.waiting(true);
        $.ajax({
            type:"POST",
            url: ajax_url,
            dataType:"json",
            data: {
                func: "database_createCollection",
                params: {
                    id: ConfigModel.database().account().collection().id,
                    name: collection.newCollectionName
                }
            },
            success: function(data) {
                collection.waiting(false);
                console.log(data);
                if (data !== false)
                {
                    ConfigModel.database().collections.push(data);
                    collection.collection(data);
                } else {
                    alert("[{oxmultilang ident='HDIO2C_ERROR_COLLECTIONEXISTS' }]");
                }
            },
            error: function(data) {
                collection.waiting(false);
                alert("[{oxmultilang ident='HDIO2C_ERROR '}]");
            }
        });
    },
    databaseOK: ko.observable(false),
    //Determines if Automatic installing of Database and Selection should be performed or manuel mapping
    newAuto: ko.observable(0),
    newDbName: ko.observable(""),
    //Invokes Autoinstalling
    createAuto: function() {
        $("#hdio2c *").addClass("waitingcursor");
        alert("[{oxmultilang ident='HDIO2C_CREATINGSTUFF1' }]" + this.newDbName() + "[{oxmultilang ident='HDIO2C_CREATINGSTUFF2' }]");
        $.ajax({
            type:"POST",
            url: ajax_url,
            dataType:"json",
            data: {
                scope: "oxid",
                func: "createDatabaseAndCollections",
                name: this.newDbName()
            },
            success: function(data)
            {
                if (data.result === true)
                {
                    alert("[{oxmultilang ident='HDIO2C_CREATINGSUCCESSFUL' }]");
                    ConfigModel.databases([]);
                    getDatabases();
                    load(data.config);
                }
                else {
                    alert(data.message);
                }
                $(".waitingcursor").removeClass("waitingcursor");

            },
            error: function(data)
            {
                $(".waitingcursor").removeClass("waitingcursor");
                alert("[{oxmultilang ident='HDIO2C_ERROR_BADNEWS' }] ");

            }
        });
    },
    saveDatabase: function()
    {
        $.ajax({
            type:"POST",
            url: ajax_url,
            dataType:"json",
            data: {
                scope:"oxid",
                func: "saveConfig",
                obj: ko.toJSON(ConfigModel)
            },
            success: function(data)
            {
                if (data.result === true)
                {
                    alert("[{oxmultilang ident='HDIO2C_CONFIGSAVED' }]");
                }
                else {
                    alert(data.message);
                }
            },
            error: function()
            {
                alert("[{oxmultilang ident='HDIO2C_ERROR_BADNEWS' }] ");
            }
        });
    }
};

//Checks if entered LoginValues are Valid
function checkstatus()
{
    ConfigModel.loginOK(false);
    if (ConfigModel.host() === "" || ConfigModel.account() === "" || ConfigModel.password() === "" || ConfigModel.user() === "")
    {
        ConfigModel.loginStatus("[{ oxmultilang ident='HDIO2C_FILL_ALL'}]");
        return;
    }

    ConfigModel.loginStatus("[{ oxmultilang ident='HDIO2C_CHECKING'}]<span class='wait'></span>");

    $.ajax({
        type:"POST",
        url: ajax_url,
        dataType:"json",
        data: {
            scope: "oxid",
            func: "checkConnection",
            user: ConfigModel.user(),
            pass: ConfigModel.password(),
            host: ConfigModel.host(),
            account: ConfigModel.account()

        },
        success: function(data) {

            if (data.result === true)
            {
                ConfigModel.loginStatus("[{ oxmultilang ident='HDIO2C_CONNECTION_CONFIRMED'}]");
                ConfigModel.loginOK(true);
                getDatabases();
            } else {

                ConfigModel.loginStatus(data.result);
                ConfigModel.databases([]);
            }
        },
        error: function(data) {
            ConfigModel.loginStatus("[{ oxmultilang ident='HDIO2C_BAD_URL'}]");
            ConfigModel.databases([]);
        }
    });
}

//gets All Databases within entered Account
function getDatabases()
{
    ConfigModel.waiting().database(true);
    $.ajax({
        type:"POST",
        url: ajax_url,
        dataType:"json",
        data: {
            func: "Account_databases"
        },
        success: function(data) {
            ConfigModel.waiting().database(false);
            var arr = data.items;
            arr.unshift({id:-1, name:'<Neu...>', newName:""});
            ConfigModel.databases(arr);
        },
        error: function(data) {
            ConfigModel.databases([{id:-2, name:"[{oxmultilang ident='HDIO2C_ERROR_UNEXPECTED' }]"}]);
            ConfigModel.waiting().database(false);
        }
    });
}

ConfigModel.host.subscribe(function() {
    checkstatus();
});

ConfigModel.user.subscribe(function() {
    checkstatus();
});

ConfigModel.account.subscribe(function() {
    checkstatus();
});

ConfigModel.password.subscribe(function() {
    checkstatus();
});


//Subscribes to selecting of Database/ Retrieves Database Fields and Collections
ConfigModel.database().account().collection.subscribe(function(item) {

    if (item === undefined || item.id <= 0)
    {
        ConfigModel.database().account().fields([]);
        ConfigModel.database().orders().fields([]);
        ConfigModel.database().orderItems().fields([]);
        ConfigModel.database().basket().fields([]);
        ConfigModel.database().addresses().fields([]);
        ConfigModel.database().collections([]);
        return;
    }

    ConfigModel.database().account().waiting(true);
    ConfigModel.waiting().collections(true);
    $.ajax({
        type:"POST",
        url: ajax_url,
        dataType:"json",
        data: {
            func: "Database_fields",
            params:{
                id: item.id,
                allproperties: 1
            }
        },
        success: function(data) {
            ConfigModel.database().account().waiting(false);
            var arr = data.items;
            arr.unshift({id:-1, name:'<Neu...>', newName:ko.observable('')});
            ConfigModel.database().account().fields(arr);
        },
        error: function(data) {
            ConfigModel.database().account().fields([]);
            ConfigModel.database().account().waiting(false);
        }
    });

    $.ajax({
        type:"POST",
        url: ajax_url,
        dataType:"json",
        data: {
            func: "Database_collections",
            params:{
                id: item.id,
                allproperties: 1
            }
        },
        success: function(data) {
            ConfigModel.waiting().collections(false);
            var arr = data.items;
            arr.unshift({id:-1, name:'<Neu...>', newName:''});
            ConfigModel.database().collections(arr);
        },
        error: function(data) {
            ConfigModel.database().collections([]);
            ConfigModel.waiting().collections(false);
        }
    });
});

ConfigModel.database().products().collection.subscribe(function(item) {
    ConfigModel.database().products().waiting(true);
    $.ajax({
        type:"POST",
        url: ajax_url,
        dataType:"json",
        data: {
            func: "Database_fields",
            params:{
                id: item.id,
                allproperties: 1
            }
        },
        success: function(data) {
            ConfigModel.database().products().waiting(false);
            var arr = data.items;
            arr.unshift({id:-1, name:'<Neu...>', newName:ko.observable('')});
            ConfigModel.database().products().fields(arr);
        },
        error: function(data) {
            ConfigModel.database().products().fields([]);
            ConfigModel.database().products().waiting(false);
        }
    });
});



//gets ColelctionFields
function getCollectionFields(id, collection)
{
    ConfigModel.database()[collection]().waiting(true);
    var arr = [];
    $.ajax({
        type:"POST",
        url: ajax_url,
        dataType: "json",
        data: {
            func: "collection_fields",
            params: {
                id: id,
                allproperties: 1
            }
        },
        success: function(data) {
            arr = data.items;
            arr.unshift({id:-1, name:'<Neu...>'});
            ConfigModel.database()[collection]().fields(arr);
            ConfigModel.database()[collection]().waiting(false);
        },
        error: function() {
            ConfigModel.database()[collection]().fields([]);
            ConfigModel.database()[collection]().waiting(false);
        }
    });

}


function checkCollectionSelected(collectionid)
{
    var count = 0;

    if (ConfigModel.database().orders().collection() !== undefined) {
        if (ConfigModel.database().orders().collection().id == collectionid) {
            count++;
        }
    }
    if (ConfigModel.database().orderItems().collection() !== undefined) {
        if (ConfigModel.database().orderItems().collection().id == collectionid) {
            count++;
        }
    }
    if (ConfigModel.database().basket().collection() !== undefined) {
        if (ConfigModel.database().basket().collection().id == collectionid) {
            count++;
        }
    }
    if (ConfigModel.database().addresses().collection() !== undefined) {
        if (ConfigModel.database().addresses().collection().id == collectionid) {
            count++;
        }
    }
    if (count >= 2) {
        return false;
    }
    return true;
}


ConfigModel.database().orders().collection.subscribe(function(item) {
    if (item === undefined || item.id < 0) {
        ConfigModel.database().orders().fields([]);
        return;
    }
    if (checkCollectionSelected(item.id)) {
        getCollectionFields(item.id, "orders");
    }
    else {
        ConfigModel.database().orders().collection(undefined);
        alert("[{oxmultilang ident='HDIO2C_ERROR_hmmgibtsschon' }]");
    }
});

ConfigModel.database().basket().collection.subscribe(function(item) {
    if (item === undefined || item.id < 0)
    {
        ConfigModel.database().basket().fields([]);
        return;
    }
    if (checkCollectionSelected(item.id))
    {
        getCollectionFields(item.id, "basket");
    }
    else {
        ConfigModel.database().basket().collection(undefined);
        alert("[{oxmultilang ident='HDIO2C_ERROR_hmmgibtsschon' }]");
    }
});

ConfigModel.database().orderItems().collection.subscribe(function(item) {
    if (item === undefined || item.id < 0)
    {
        ConfigModel.database().orderItems().fields([]);
        return;
    }
    if (checkCollectionSelected(item.id))
    {
        getCollectionFields(item.id, "orderItems");
    }
    else {
        ConfigModel.database().orderItems().collection(undefined);
        alert("[{oxmultilang ident='HDIO2C_ERROR_hmmgibtsschon' }]");
    }
});

ConfigModel.database().addresses().collection.subscribe(function(item) {
    if (item === undefined || item.id < 0)
    {
        ConfigModel.database().addresses().fields([]);
        return;
    }
    if (checkCollectionSelected(item.id))
    {
        getCollectionFields(item.id, "addresses");
    }
    else {
        ConfigModel.database().addresses().collection(undefined);
        alert("[{oxmultilang ident='HDIO2C_ERROR_hmmgibtsschon' }]");
    }
});

function checkFieldSelected(field)
{
    if (field.match() === undefined) {
        return;
    }
    if (field.match().id == -1) {
        return;
    }
    var count = 0;
    ko.utils.arrayFilter(ConfigModel.database()[field.collection]().fieldMatching(), function(item) {
        if (item.match() !== undefined)
        {
            if (item.match().id != -1 && item.match().id == field.match().id)
            {
                count++;
            }
        }
    });

    if (count >= 2) {
        alert("[{oxmultilang ident='HDIO2C_ERROR_hmmgibtsschon' }]");
        field.match(undefined);
    }


}


//checks if alle prerequisites are fulfilled to proceed to Save.
ConfigModel.allowSave = ko.computed(function()
{
    var foundError = false;
    ko.utils.arrayFilter(ConfigModel.database().account().fieldMatching(), function(item) {
        if (item.match() === undefined)
        {
            foundError = true;
            return;
        }
    });
    if (foundError) {
        return false;
    }

    ko.utils.arrayFilter(ConfigModel.database().orders().fieldMatching(), function(item) {
        if (item.match() === undefined)
        {
            foundError = true;
        }
    });
    if (foundError) {
        return false;
    }

    ko.utils.arrayFilter(ConfigModel.database().orderItems().fieldMatching(), function(item) {
        if (item.match() === undefined) {
            foundError = true;
        }
    });
    if (foundError) {
        return false;
    }

    ko.utils.arrayFilter(ConfigModel.database().basket().fieldMatching(), function(item) {
        if (item.match() === undefined) {
            foundError = true;
        }
    });
    if (foundError) {
        return false;
    }


    ko.utils.arrayFilter(ConfigModel.database().addresses().fieldMatching(), function(item) {
        if (item.match() === undefined) {
            foundError = true;
        }
    });
    if (foundError) {
        return false;
    }

    return true;

});

ko.applyBindings(ConfigModel);
//checks Status of Login Information
checkstatus();


//Some UI behavior

$("#hdio2c").on('change', ".field", function() {
    checkFieldSelected(ko.dataFor(this));
});

$("#hdio2c").on('focus', ".newInput", function() {
    if ($(this).val() == $(this).attr("def")) {
        $(this).val("");
    }
});
$("#hdio2c").on('blur', ".newInput", function() {
    if ($(this).val() === "") {
        $(this).val($(this).attr("def"));
    }
});


function findField(id, _array)
{
    var Field = null;
    ko.utils.arrayFilter(_array, function(item) {
        if (item.id == id)
        {
            Field = item;
        }
    });
    return Field;
}

function load(config)
{
    console.log(config);
    ConfigModel.newAuto(1);
    ConfigModel.waiting().loading(true);
    readycount = 0

    $('#databasePanel *').addClass("waitingcursor");

    var checkDatabases = setInterval(function() {
        if (ConfigModel.databases().length != 0)
        {
            ko.utils.arrayFilter(ConfigModel.databases(), function(item) {
                if (item.id == config.account.id)
                {
                    ConfigModel.database().account().collection(item);
                }
                if (item.id == config.products.id)
                {
                    ConfigModel.database().products().collection(item);
                }
            }

            );
            clearInterval(checkDatabases);
            readycount++;
        }

    }, 1000);
    var checkCollections = setInterval(function() {
        if (ConfigModel.database().collections().length != 0)
        {
            ko.utils.arrayFilter(ConfigModel.database().collections(), function(item) {
                if (item.id == config.orders.id)
                {
                    ConfigModel.database().orders().collection(item);
                    return;
                }
                if (item.id == config.orderItems.id)
                {
                    ConfigModel.database().orderItems().collection(item);
                    return;
                }
                if (item.id == config.basket.id)
                {
                    ConfigModel.database().basket().collection(item);
                    return;
                }
                if (item.id == config.addresses.id)
                {
                    ConfigModel.database().addresses().collection(item);
                    return;
                }
            })
            clearInterval(checkCollections);
            readycount++;
        }


    }, 1000);

    var checkAccount = setInterval(function() {
        if (ConfigModel.database().account().fields().length != 0)
        {
            ko.utils.arrayForEach(ConfigModel.database().account().fieldMatching(), function(item) {
                item.match(findField(config.account.fieldMatching[item.internal].id, ConfigModel.database().account().fields()))
            });

            clearInterval(checkAccount);
            readycount++;
        }

    }, 1000);

    var checkProducts = setInterval(function() {
        if (ConfigModel.database().products().fields().length != 0)
        {
            ko.utils.arrayForEach(ConfigModel.database().products().fieldMatching(), function(item) {
                item.match(findField(config.products.fieldMatching[item.internal].id, ConfigModel.database().products().fields()))
            });

            clearInterval(checkProducts);
            readycount++;
        }

    }, 1000);

    var checkOrders = setInterval(function() {
        if (ConfigModel.database().orders().fields().length != 0)
        {
            ko.utils.arrayForEach(ConfigModel.database().orders().fieldMatching(), function(item) {
                item.match(findField(config.orders.fieldMatching[item.internal].id, ConfigModel.database().orders().fields()))
            });

            clearInterval(checkOrders);
            readycount++;
        }
    }, 1000);
    var checkOrderItems = setInterval(function() {
        if (ConfigModel.database().orderItems().fields().length != 0)
        {
            ko.utils.arrayForEach(ConfigModel.database().orderItems().fieldMatching(), function(item) {
                item.match(findField(config.orderItems.fieldMatching[item.internal].id, ConfigModel.database().orderItems().fields()))
            });

            clearInterval(checkOrderItems);
            readycount++;
        }

    }, 1000);
    var checkBasket = setInterval(function() {
        if (ConfigModel.database().basket().fields().length != 0)
        {
            ko.utils.arrayForEach(ConfigModel.database().basket().fieldMatching(), function(item) {
                item.match(findField(config.basket.fieldMatching[item.internal].id, ConfigModel.database().basket().fields()))
            });

            clearInterval(checkBasket);
            readycount++;
        }

    }, 1000);
    var checkAddresses = setInterval(function() {
        if (ConfigModel.database().addresses().fields().length != 0)
        {
            ko.utils.arrayForEach(ConfigModel.database().addresses().fieldMatching(), function(item) {
                item.match(findField(config.addresses.fieldMatching[item.internal].id, ConfigModel.database().addresses().fields()))
            });

            clearInterval(checkAddresses);
            readycount++;
        }

    }, 1000);

    var checkFinished = setInterval(function() {
        if (readycount == 8)
        {
            $('#databasePanel *').removeClass("waitingcursor");
            ConfigModel.waiting().loading(false);
            clearInterval(checkFinished);
        }

    }, 1000)

}


[{if $config != 'false'}]
        var config = [{$config}];

load(config);
[{ / if }]