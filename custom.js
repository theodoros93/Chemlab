var myChart = null;
var is_complete = false;
var is_saved = false;
var stage = 1;
var stage_length = 0;
var max_stages = 5;
var max_stage_length = 10;

var material_body_row = ['count', 'name', 'cas', 'density', 'mw', 'price/kg', 'unit', 'qty', 'cc/kg input', 'cc/kg output', 'cc/kg api', 'actual cc/kg api', 'cost', 'mol', 'mol ratio', 'contribution', 'Delete'];
var inputs = {
    'body': {
        'stage_1': {
            'stage_length_1': {}, 
            'stage_length_2': {},
            'stage_length_3': {},
            'stage_length_4': {},
            'stage_length_5': {},
            'stage_length_6': {},
            'stage_length_7': {},
            'stage_length_8': {},
            'stage_length_9': {},
            'stage_length_10': {}
        },
        'stage_2': {
            'stage_length_1': {}, 
            'stage_length_2': {},
            'stage_length_3': {},
            'stage_length_4': {},
            'stage_length_5': {},
            'stage_length_6': {},
            'stage_length_7': {},
            'stage_length_8': {},
            'stage_length_9': {},
            'stage_length_10': {}
        },
        'stage_3': {
            'stage_length_1': {}, 
            'stage_length_2': {},
            'stage_length_3': {},
            'stage_length_4': {},
            'stage_length_5': {},
            'stage_length_6': {},
            'stage_length_7': {},
            'stage_length_8': {},
            'stage_length_9': {},
            'stage_length_10': {}
        },
        'stage_4': {
            'stage_length_1': {}, 
            'stage_length_2': {},
            'stage_length_3': {},
            'stage_length_4': {},
            'stage_length_5': {},
            'stage_length_6': {},
            'stage_length_7': {},
            'stage_length_8': {},
            'stage_length_9': {},
            'stage_length_10': {}
        },
        'stage_5': {
            'stage_length_1': {}, 
            'stage_length_2': {},
            'stage_length_3': {},
            'stage_length_4': {},
            'stage_length_5': {},
            'stage_length_6': {},
            'stage_length_7': {},
            'stage_length_8': {},
            'stage_length_9': {},
            'stage_length_10': {}
        },
    },
    'footer_1': {},
    'footer_2': {},
    'footer_3': {},
    'footer_4': {},
    'footer_5': {},
};

//console.log(inputs);

if ( typeof toastr !== 'undefined' ){
    toastr.options = {"timeOut": "15000", "progressBar": true};
}
var text_keys = ['name', 'unit'];
var input_props = ' step="0.01" min="0" max="9999" maxlength="50" class="form-control" ';

function loadTableFromDB() {
    //TODO: get table and load it
}


$(document).ready(function(){

    initDatePicker();
    initDateRangePicker();


    //CHOSEN IN MATERIAL PRICING
    if ( $('select#materials-prices').length ){ 
        $('select#materials-prices').chosen().on('change', function(evt, params){
            var date = moment().format('YYYY-MM-DD');
            window.location.href = 'material_price.php?mid=' + params.selected + '&date='+date;
        });
    }


    //CHOSEN IN API STAGES
    if ( $('select#materials').length ){ 
        $('select#materials').chosen();
    }


    //CHOSEN IN CHARTS
    if ( $('select#materials-chart').length ){ 
        $('select#materials-chart').chosen();
    }
    

    //PREVENT USER TO SAVE PRICE FOR PAST DATES
    $(this).on('submit', 'form#material-price-form', function(e){
        var dateParam = getUrlParameter(window.location.href, 'date');
        if ( dateParam.length ){
            if ( moment(dateParam).isBefore() === true && moment().isSame(moment(dateParam), 'day') === false ){
                e.preventDefault();
                alert('Δεν επιτρέπεται η αλλαγή τιμής για παρελθοντικές ημερομηνίες');
            }
        }
    });


    //MATERIALS PAGE: EDIT MATERIAL
    $(this).on('click', 'button.edit-material-btn', function(){
        $(this).closest('tr').find('input').removeAttr('readonly');
        $(this).closest('tr').find('input').each(function(){
            $(this).attr('name', $(this).attr('data-name'));
        });
        $(this).html('&#10004;');
        $(this).removeClass('btn-default').addClass('btn-success');
    });


    //MATERIALS PAGE: SUBMIT MATERIAL
    $(this).on('click', 'button.edit-material-btn.btn-success', function(){
        $('#edit-materials-form').trigger('submit');
    });


    //MATERIALS PAGE: ADD MATERIAL
    $(this).on('click', '#add-new-material-btn', function(){
        $('#new-material-form').show();
        $("html, body").animate({ scrollTop: $(document).height() }, 500);
    });


    //MATERIALS PAGE: DELETE MATERIAL
    $(this).on('click', 'button.remove-material-btn', function(){
        $(this).closest('tr').find('input[data-name="id"]').attr('name', 'id');
        var res = confirm('Are you sure you want to delete this material?');
        if ( res ){
            $('#edit-materials-form').attr('action', 'delete-material.php');
            $('#edit-materials-form').trigger('submit');
        }
    });


    //STAGES: EXPORT PDF
    $(this).on('click', '#export-pdf-btn', function(){

        //$('#debug-btn').trigger('click');

        var name = $('tr.footer:last input[data-key="output"]').val();
        var cost = $('tr.footer:last input[data-key="eur"]').val();
        var density = $('tr.footer:last input[data-key="density"]').val();
        var mw = $('tr.footer:last input[data-key="mw"]').val();
        var qty_out = $('tr.footer:last input[data-key="qty out"]').val();
        var w_w_yield = $('tr.footer:last input[data-key="w/w yield"]').val();
        var cc_kg_output = $('tr.footer:last input[data-key="cc/kg output"]').val();
        var api_cc = $('tr.footer:last input[data-key="api cc"]').val();
        var actual_cc_kg_api = $('tr.footer:last input[data-key="actual cc/kg api"]').val();
        var mol = $('tr.footer:last input[data-key="mol"]').val();
        var mol_yield = $('tr.footer:last input[data-key="mol yield"]').val();
        var contribution = $('tr.footer:last input[data-key="contribution"]').val();

        var $obj = $('#stages').clone();
        $obj.find('th:last').remove();
        $obj.find('td:last-child').remove();
        $obj.find('input').removeClass('form-control');
        $obj.find('input').removeAttr('readonly');
        //NORMALIZE VALUES
        $obj.find('tbody tr input').each(function(){
            var val = $(this).val();
            //console.log(val);
            if ( val !== '' ){
                $(this).attr('value', val);
            }
        });
        //NORMALIZE STAGE TEXTS
        var normalized_stage_text = 0;
        $obj.find('tbody tr[data-stage]').each(function(){
            var stage = parseInt($(this).attr('data-stage'));
            if ( stage != normalized_stage_text ){
                $(this).html('<td class="stage-text" colspan="16">Stage '+stage+'</td>');
                normalized_stage_text++;
            }
        });
        var content = $obj.html();
        //console.log()
        //return;
        $.ajax({
            data: {
                'content': content, 'name': name, 'total_stages': stage, 'total_cost': cost, 'density': density, 'mw': mw, 'qty_out': qty_out, 'w_w_yield': w_w_yield,
                'cc_kg_output': cc_kg_output, 'api_cc': api_cc, 'actual_cc_kg_api': actual_cc_kg_api, 'mol': mol, 'mol_yield': mol_yield, 'contribution': contribution
            },
            type: 'post',
            url: 'export.php',
            complete: function(res){
                var url = 'http://localhost/chemlab/export.php';
                var win = window.open(url, '_blank');
                win.focus();
            }
        });
    });


//STAGES: SAVE LABS
    $(this).on('click', '#export-lab-btn', function(){

        //$('#debug-btn').trigger('click');

        var name = $('tr.footer:last input[data-key="output"]').val();
        var cost = $('tr.footer:last input[data-key="eur"]').val();
        var density = $('tr.footer:last input[data-key="density"]').val();
        var mw = $('tr.footer:last input[data-key="mw"]').val();
        var qty_out = $('tr.footer:last input[data-key="qty out"]').val();
        var w_w_yield = $('tr.footer:last input[data-key="w/w yield"]').val();
        var cc_kg_output = $('tr.footer:last input[data-key="cc/kg output"]').val();
        var api_cc = $('tr.footer:last input[data-key="api cc"]').val();
        var actual_cc_kg_api = $('tr.footer:last input[data-key="actual cc/kg api"]').val();
        var mol = $('tr.footer:last input[data-key="mol"]').val();
        var mol_yield = $('tr.footer:last input[data-key="mol yield"]').val();
        var contribution = $('tr.footer:last input[data-key="contribution"]').val();

        var $obj = $('#stages').clone();
        
        //NORMALIZE VALUES
        $obj.find('tbody tr input').each(function(){
            var val = $(this).val();
            //console.log(val);
            if ( val !== '' ){
                $(this).attr('value', val);
            }
        });
        //NORMALIZE STAGE TEXTS
        var normalized_stage_text = 0;
        $obj.find('tbody tr[data-stage]').each(function(){
            var stage = parseInt($(this).attr('data-stage'));
            if ( stage != normalized_stage_text ){
                $(this).html('<td class="stage-text" colspan="17">Stage '+stage+'</td>');
                normalized_stage_text++;
            }
        });
        var content = $obj.html();
        //console.log()
        //return;
        $.ajax({
            data: {
                'content': content, 'name': name, 'total_stages': stage, 'total_cost': cost, 'density': density, 'mw': mw, 'qty_out': qty_out, 'w_w_yield': w_w_yield,
                'cc_kg_output': cc_kg_output, 'api_cc': api_cc, 'actual_cc_kg_api': actual_cc_kg_api, 'mol': mol, 'mol_yield': mol_yield, 'contribution': contribution
            },
            type: 'post',
            url: 'labexp.php',
                    });
    });







    //STAGES: TEST
    $(this).on('click', '#test-btn', function(){
        setRandomValues('body');
        setRandomValues('footer');
        calculateHelper();
    });
    //ADD MATERIAL
    $(this).on('click', '#add-material-btn', addMaterialToStage);
    //ADD STAGE
    $(this).on('click', '#add-stage-btn', addStage);
    //CALCULATE
    $(this).on('click', '#calculate-btn', calculateHelper);
    //DELETE MATERIAL
    $(this).on('click', 'input.delete-material-btn', function(){ deleteMaterialFromStage($(this)); });
    //add material to each stage
    $(this).on('click', '#add-mat-btn', addMaterialToStage2);
     $(this).on('click', '#add-material2-btn', addmaterialeachstage);
    //SAVE NEW MATERIAL
    $(this).on('click', '#save-new-material-btn', saveNewMaterial);
    //DEBUG
    $(this).on('click', '#debug-btn', function(){ debugScenario(1); });

    function debugScenario(scenario){
        if ( scenario == 1 ){
            $('#materials').val(48); $('#add-material-btn').trigger('click');
            $('#materials').val(36); $('#add-material-btn').trigger('click');
            $('#materials').val(37); $('#add-material-btn').trigger('click');
            $('#materials').val(38); $('#add-material-btn').trigger('click');
            $('#materials').val(39); $('#add-material-btn').trigger('click');
            $('#materials').val(40); $('#add-material-btn').trigger('click');

            //ADD PRICES
            $('input[data-stage="1"][data-stage_length="1"][data-key="price/kg"]').val(8594);
            $('input[data-stage="1"][data-stage_length="2"][data-key="price/kg"]').val(2300);
            $('input[data-stage="1"][data-stage_length="3"][data-key="price/kg"]').val(122);
            $('input[data-stage="1"][data-stage_length="4"][data-key="price/kg"]').val(28);
            $('input[data-stage="1"][data-stage_length="5"][data-key="price/kg"]').val(32);
            $('input[data-stage="1"][data-stage_length="6"][data-key="price/kg"]').val(1);

            //ADD QTY
            $('input[data-stage="1"][data-stage_length="1"][data-key="qty"]').val(30);
            $('input[data-stage="1"][data-stage_length="2"][data-key="qty"]').val(11.25);
            $('input[data-stage="1"][data-stage_length="3"][data-key="qty"]').val(68);
            $('input[data-stage="1"][data-stage_length="4"][data-key="qty"]').val(13.8);
            $('input[data-stage="1"][data-stage_length="5"][data-key="qty"]').val(9.3);
            $('input[data-stage="1"][data-stage_length="6"][data-key="qty"]').val(68.2);

            //CC/KG INPUT
            $('input[data-stage="1"][data-stage_length="1"][data-key="cc/kg input"]').val(1);
            //MOL RATIO
            $('input[data-stage="1"][data-stage_length="1"][data-key="mol ratio"]').val(1);

            //FOOTER
            $('tr.footer[data-stage="1"] input[data-key="output"]').val('Output A');
            $('tr.footer[data-stage="1"] input[data-key="mw"]').val(454.46);
            $('tr.footer[data-stage="1"] input[data-key="qty out"]').val(35.96);
            $('tr.footer[data-stage="1"] input[data-key="cc/kg output"]').val(1);
            $('tr.footer[data-stage="1"] input[data-key="density"]').val(1);

            calculateHelper();
            addStage();

            //STAGE 2
            $('#materials').val(41); $('#add-material-btn').trigger('click');
            $('#materials').val(42); $('#add-material-btn').trigger('click');
            $('#materials').val(43); $('#add-material-btn').trigger('click');
            $('#materials').val(44); $('#add-material-btn').trigger('click');
            $('#materials').val(45); $('#add-material-btn').trigger('click');
            $('#materials').val(46); $('#add-material-btn').trigger('click');
            $('#materials').val(47); $('#add-material-btn').trigger('click');

            //ADD PRICES
            $('input[data-stage="2"][data-stage_length="1"][data-key="price/kg"]').val(8139);
            $('input[data-stage="2"][data-stage_length="2"][data-key="price/kg"]').val(200);
            $('input[data-stage="2"][data-stage_length="3"][data-key="price/kg"]').val(3719);
            $('input[data-stage="2"][data-stage_length="4"][data-key="price/kg"]').val(147);
            $('input[data-stage="2"][data-stage_length="5"][data-key="price/kg"]').val(28);
            $('input[data-stage="2"][data-stage_length="6"][data-key="price/kg"]').val(6);
            $('input[data-stage="2"][data-stage_length="7"][data-key="price/kg"]').val(32);
            $('input[data-stage="2"][data-stage_length="8"][data-key="price/kg"]').val(1);

            //ADD QTY
            $('input[data-stage="2"][data-stage_length="2"][data-key="qty"]').val(9);
            $('input[data-stage="2"][data-stage_length="3"][data-key="qty"]').val(34);
            $('input[data-stage="2"][data-stage_length="4"][data-key="qty"]').val(62);
            $('input[data-stage="2"][data-stage_length="5"][data-key="qty"]').val(512);
            $('input[data-stage="2"][data-stage_length="6"][data-key="qty"]').val(60);
            $('input[data-stage="2"][data-stage_length="7"][data-key="qty"]').val(1);
            $('input[data-stage="2"][data-stage_length="8"][data-key="qty"]').val(360);

            //MOL RATIO
            $('input[data-stage="2"][data-stage_length="1"][data-key="mol ratio"]').val(1);

            //FOOTER
            $('tr.footer[data-stage="2"] input[data-key="output"]').val('Output B');
            $('tr.footer[data-stage="2"] input[data-key="mol ratio"]').val(1);
            $('tr.footer[data-stage="2"] input[data-key="mw"]').val(468.49);
            $('tr.footer[data-stage="2"] input[data-key="qty out"]').val(26.5);
            $('tr.footer[data-stage="2"] input[data-key="cc/kg output"]').val(1);
            $('tr.footer[data-stage="2"] input[data-key="density"]').val(1);
        } 
        else if ( scenario == 2 ){
            $('#materials').val(36); $('#add-material-btn').trigger('click');
            $('#materials').val(37); $('#add-material-btn').trigger('click');

            $('input[data-stage="1"][data-stage_length="1"][data-key="mol ratio"]').val(1);

            $('input[data-stage="1"][data-stage_length="1"][data-key="density"]').val(4);
            $('input[data-stage="1"][data-stage_length="2"][data-key="density"]').val(43);

            $('input[data-stage="1"][data-stage_length="1"][data-key="mw"]').val(563);
            $('input[data-stage="1"][data-stage_length="2"][data-key="mw"]').val(363);

            $('input[data-stage="1"][data-stage_length="1"][data-key="price/kg"]').val(25);
            $('input[data-stage="1"][data-stage_length="2"][data-key="price/kg"]').val(44);

            $('input[data-stage="1"][data-stage_length="1"][data-key="qty"]').val(1);
            $('input[data-stage="1"][data-stage_length="2"][data-key="qty"]').val(2);

            $('input[data-stage="1"][data-stage_length="1"][data-key="cc/kg input"]').val(1);

            $('tr.footer[data-stage="1"] input[data-key="output"]').val('Output C');
            $('tr.footer[data-stage="1"] input[data-key="density"]').val(1);
            $('tr.footer[data-stage="1"] input[data-key="qty out"]').val(3);
            $('tr.footer[data-stage="1"] input[data-key="mw"]').val(124);
            $('tr.footer[data-stage="1"] input[data-key="cc/kg output"]').val(1);

            calculateHelper();
            addStage();

            $('#materials').val(3); $('#add-material-btn').trigger('click');

            $('input[data-stage="2"][data-stage_length="1"][data-key="mol ratio"]').val(1);

            $('input[data-stage="2"][data-stage_length="2"][data-key="qty"]').val(2);

            $('tr.footer[data-stage="2"] input[data-key="output"]').val('Output F');
            $('tr.footer[data-stage="2"] input[data-key="density"]').val(1);
            $('tr.footer[data-stage="2"] input[data-key="mw"]').val(250);
            $('tr.footer[data-stage="2"] input[data-key="qty out"]').val(2);
            $('tr.footer[data-stage="2"] input[data-key="cc/kg output"]').val(1);

            calculateHelper();
        }
    }


    //SET START & END DATE OF DATERANGEPICKER
    $(this).on('click', '#select-daterangepicker', function(){
        var range = $('#daterangepicker').val();
        //console.log(range);
        if ( range.indexOf('-') != -1 ){
            var arr = range.split('-');
            var start = $.trim(arr[0]);
            var end = $.trim(arr[1]);
            //console.log(start, end);
            $('#daterangepicker').data('daterangepicker').setStartDate(start);
            $('#daterangepicker').data('daterangepicker').setEndDate(end);
            getSelectedMaterialPrices(moment(start, 'DD/MM/YYYY'), moment(end, 'DD/MM/YYYY'));
        }
    });


    //STAGES: COMPLETE STAGES
    $(this).on('click', '#complete-btn', function(){
        var $last = $('#stages tbody tr.footer input[data-key="api cc"]:last');
        if ( !$last.length ){
            return;
        } else{
            if ( $last.val() === '' ){
                var response = confirm('Είστε σίγουροι για την ολοκλήρωση;');
                if ( response ){
                    $last.val(1);
                    //for (var i=0; i<=stage; i++)
                    //{
                        //var k=stage;
                        //while (k--)
                       // {

                            calculateHelper('complete');
                            
                       // }
                        
                    
                //}
                }
            } else{
                   //for (var i=0; i<=stage; i++)
                   // {
                    calculateHelper('complete');
               // }
            }
        }
    });

});

function setRandomValues(position){
    var test_values = {
        'body': ['cc/kg output', 'cc/kg input', 'mol ratio', 'unit', 'qty'],
        'footer': {
            'output': 'Out material '+stage, 
            'density': 1,
            'mw': '',
            'eur': '',
            'usd': '',
            'qty out': '',
            'w/w yield': '',
            'cc/kg output': 1,
            'mol': '',
            'mol yield': '',
        }
    };
    if ( position == 'body' ){
        for(var i = 1; i <= stage; i++){
            for(var j = 1; j <= stage_length; j++){
                for(var k = 0; k < test_values['body'].length; k++){
                    var key = test_values['body'][k];
                    //console.log('#stages tbody td input[data-key="'+key+'"][data-stage='+i+'][data-stage_length='+j+']');
                    $('#stages tbody td input[data-key="'+key+'"][data-stage='+i+'][data-stage_length='+j+']').each(function(){
                        var value = '';
                        if ( key == 'mol ratio' || key == 'cc/kg input' ){
                            if ( j == 1 ){
                                value = 1;
                            } else{
                                value = '';
                            }
                        } else if ( key == 'unit' ){
                            value = 'kg';
                        } else if ( key == 'qty' ){
                            value = randomIntegerRange(10);
                        } else{
                            value = randomIntegerRange();
                        }
                        $(this).val(value);
                    });
                }
            }
        }
    }
    if ( position == 'footer' ){
        for(var i = 1; i <= stage; i++){
            var eur = '';
            for(key in test_values['footer']){
                var value = test_values['footer'][key];
                //console.log(value);
                if ( key == 'mw' ){
                    value = randomIntegerRange();
                } else if ( key == 'qty out' ){
                    value = randomIntegerRange(2);
                } else if ( key == 'eur' ){
                    value = eur = randomIntegerRange();
                } else if ( key == 'usd' ){
                    value = calculateUSDFromEUR(eur);
                }
                $('#stages tr.footer[data-stage='+i+'] td input[data-key="'+key+'"]').val(value);
            }
        }
    }
}


function calculateHelper(mode){
    if ( stage_length == 0 ){
        alert('Εισάγετε στοιχεία σε κάθε στάδιο');
    } else{
        for(var i = 1; i <= stage; i++){
            var currentStageLength = $('input[data-key="count"][data-stage="' + i + '"]').last().val();
            //FOOTER
            severCalculate('footer', i, 0, 'w/w yield');
            severCalculate('footer', i, 0, 'mol');

            //BODY
            for(var j = 1; j <= currentStageLength; j++){
                if ( j >= 2 ){
                    severCalculate('body', i, j, 'cc/kg input');
                }
                severCalculate('body', i, j, 'mol');
                if ( j >= 2 ){
                    severCalculate('body', i, j, 'mol ratio');
                }
            }
            
            for(var j = 1; j <= currentStageLength; j++){
                //BODY
                severCalculate('body', i, j, 'cc/kg output');
            }

            //FOOTER
            severCalculate('footer', i, 0, 'mol yield');
        }
        toastr.success('Οι υπολογισμοί πραγματοποιήθηκαν');

        //COMPLETION
        var k=stage;
            while(k--)
            {
        if ( mode == 'complete' ){
            for(var i = stage; i >= 1; i--) {
                var currentStageLength = $('input[data-key="count"][data-stage="' + i + '"]').last().val();
                
                if (currentStageLength === undefined) {
                    //no materials added for this stage
                    currentStageLength = 1;

                }
                for(var j = 1; j <= currentStageLength; j++) {
                    
                    fillApiCCForStage(i);
                    //BODY CC/KG API
                    severCalculate('body', i, j, 'cc/kg api');
                    //BODY ACTUAL CC/KG API
                    severCalculate('body', i, j, 'actual cc/kg api');
                    //BODY COST
                    severCalculate('body', i, j, 'cost');
                }

                //FOOTER ACTUAL CC/KG API
                severCalculate('footer', i, 0, 'actual cc/kg api');

                //FOOTER COST SUM - ELEMENT: SELECT BODY COST
                severCalculate('sum', i, 0, 'cost', $('#stages tbody tr:not(.footer) input[data-stage="'+i+'"][data-key="cost"]'));

                //FOOTER EUR & USD & PRICE/KG FOR THE NEXT STAGE
                severCalculate('footer', i, 0, 'eur-usd'); 
            }

            /*
            //FOOTER EUR & USD & PRICE/KG FOR THE NEXT STAGE
            for(var i = 1; i <= stage_length; i++){
                severCalculate('footer', i, 0, 'eur-usd'); 
            }
            */

            //RECALCULATE BODY COST SINCE PRICE/KG OF EACH STAGE IS UPDATED
            
            for(var i = stage; i >= 1; i--) {
                var currentStageLength = $('input[data-key="count"][data-stage="' + i + '"]').last().val();
                
                if (currentStageLength === undefined) {
                    //no materials added for this stage
                    currentStageLength = 1;

                }
                for(var j = 1; j <= currentStageLength; j++) {
                    //BODY COST
                    severCalculate('body', i, j, 'cost');
                }
            }

            for(var i = stage; i >= 1; i--){
                var currentStageLength = $('input[data-key="count"][data-stage="' + i + '"]').last().val();
                
                if (currentStageLength === undefined) {
                    //no materials added for this stage
                    currentStageLength = 1;

                }

                //BODY CONTRIBUTION
                for(var j = 1; j <= currentStageLength; j++){
                    //GET LAST FOOTER COST AS "TOTAL"
                    severCalculate('body', i, j, 'contribution', $('#stages tbody tr.footer input[data-key="cost"]:last').val());
                }

                //FOOTER CONTRIBUTION SUM - ELEMENT: SELECT BODY CONTRIBUTION
                severCalculate('sum', i, 0, 'contribution', $('#stages tbody tr:not(.footer) input[data-stage="'+i+'"][data-key="contribution"]'));
            }

            
            //RECALCULATION FOR COST & CONTRIBUTION SINCE VALUES ARE UPDATED//

            
            for(var i = stage; i >= 1; i--){
                //FOOTER COST SUM - ELEMENT: SELECT BODY COST
                severCalculate('sum', i, 0, 'cost', $('#stages tbody tr:not(.footer) input[data-stage="'+i+'"][data-key="cost"]'));           

                var currentStageLength = $('input[data-key="count"][data-stage="' + i + '"]').last().val();
                
                if (currentStageLength === undefined) {
                    //no materials added for this stage
                    currentStageLength = 1;

                }
                //BODY CONTRIBUTION
                for(var j = 1; j <= currentStageLength; j++){
                    //GET LAST FOOTER COST AS "TOTAL"
                    severCalculate('body', i, j, 'contribution', $('#stages tbody tr.footer input[data-key="cost"]:last').val());
                }
                //FOOTER CONTRIBUTION SUM - ELEMENT: SELECT BODY CONTRIBUTION
                severCalculate('sum', i, 0, 'contribution', $('#stages tbody tr:not(.footer) input[data-stage="'+i+'"][data-key="contribution"]'));
            }
            }
            toastr.success('Η ολοκλήρωση πραγματοποιήθηκε');
            is_complete = true;
        }

        
    }
}


function fillApiCCForStage(Stage){
    var $next = $('#stages tbody tr[data-stage="'+(Stage+1)+'"] input[data-key="cc/kg api"]:first');
    if ( $next.length ){
        var next_value = $next.val();
        $('#stages tbody tr.footer[data-stage="'+Stage+'"] input[data-key="api cc"]').val(next_value);
    }
}


function severCalculate(position, Stage, Stage_Length, field, element){
    //FOOTER CALCULATION FOR COST AND CONTRIBUTION
    if ( field == 'eur-usd' ){
        var api_cc = $('#stages tbody tr.footer[data-stage="'+Stage+'"] input[data-key="api cc"]').val();
        var cost = $('#stages tbody tr.footer[data-stage="'+Stage+'"] input[data-key="cost"]').val();
        if ( $.isNumeric(api_cc) && $.isNumeric(cost) ){
            api_cc = parseFloat(api_cc);
            cost = parseFloat(cost);
            //console.log('Stage', Stage, api_cc, cost);
            if ( api_cc != 0 && cost != 0 ){
                var eur = cost / api_cc;
                $('#stages tbody tr.footer[data-stage="'+Stage+'"] input[data-key="eur"]').val(eur);
                $('#stages tbody tr.footer[data-stage="'+Stage+'"] input[data-key="usd"]').val(calculateUSDFromEUR(eur));
                //SET PRICE FOR THE NEXT STAGE
                //console.log('Stage', Stage, api_cc, cost);
                $('#stages tbody tr:not(.footer) td input[data-stage="'+(Stage+1)+'"][data-stage_length="1"][data-key="price/kg"]').val(eur);
            }
        }
    } else if ( position == 'sum' && element.length ){
        var sum = 0;
        element.each(function(){ sum += Number($(this).val()); });
        sum = Math.round(sum);
       // if ( field == 'contribution' ){
            //console.log(Stage, '=>', sum, element);
        //}
        $('#stages tbody tr.footer[data-stage="'+Stage+'"] input[data-key="'+field+'"]').val(sum);
    } else{
        var mapping = {'footer': 'tr.footer', 'body': 'tbody'};
        var inputs = getInputs();
        if ( field == 'contribution' ){
        //if ( field == 'cost' ){
            //console.log('Stage', Stage, 'Stage Length', Stage_Length, 'Field', ' => ', field, 'Inputs', inputs['body']['stage_'+Stage]['stage_length_'+Stage_Length]);
        }
        $.ajax({
            data: {'stage': Stage, 'stage_length': Stage_Length, 'field': field, 'position': position, 'inputs': inputs, 'total': element},
            async: false,
            type: 'post',
            url: 'calculations.php',
            beforeSend: function(){
                $('input[type="button"]').attr('disabled', 'disabled');
            },
            complete: function(res){
                $('input[type="button"]').removeAttr('disabled');
                var response = res.responseText;
                if ( response.indexOf('Notice') != -1 || response.indexOf('Warning') != -1 ){
                    //console.log('Stage', Stage, 'Stage Length', Stage_Length, 'Field', '=>', field, 'Inpunts', inputs);
                    //console.log(response);
                }
                if ( response !== '' && isJson(response) ){
                    var json = $.parseJSON(response);
                    if ( json.req == 'error' ){
                        toastr.error(json.message);
                    } else if ( json.req == 'success' ){
                        var calculation = json.calculation;
                        //console.log(position, field, ' = ' + calculation);
                        if ( position == 'body' ){
                            var elem = '#stages '+mapping[position]+' td input[data-stage="'+Stage+'"][data-stage_length="'+Stage_Length+'"][data-key="'+field+'"]';
                            $(elem).val(calculation);
                        }
                        else if ( position == 'footer' ){
                            var elem = '#stages '+mapping[position]+'[data-stage="'+Stage+'"] td input[data-key="'+field+'"]';
                            //console.log(elem);
                            $(elem).val(calculation);
                        }
                    }
               }
            }
        });
    }
}


function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


function addMaterialToStage(){
    if ( !$('#materials option:selected').length || $('#materials option:selected').val() === '' ){
        return;
    }
    var material = $('#materials option:selected').attr('data-material');
    if ( material === '' ){
        return;
    }
    material = $.parseJSON(material);
    //console.log(material);
    stage_length++;
    var html = '<tr>';
    for(var i = 0; i < material_body_row.length; i++){
        var key = material_body_row[i];
        var elem = '';
        if ( key == 'Delete' ){
            elem = '<input class="btn btn-danger delete-material-btn" type="button" value="X" />';
        } else{
            var type = $.inArray(key, text_keys) != -1 ? 'text' : 'number';
            var readonly = key == 'count' ? 'readonly' : '';
            value = key == 'count' ? stage_length : material[key];
            if ( value === undefined || value === null ){
                value = '';
            }
            elem = '<input type="'+type+'" '+readonly+''+input_props+' data-stage_length="'+stage_length+'" data-stage="'+stage+'" data-key="'+key+'" value="'+value+'" />';
        }
        //console.log(key, value);
        html += '<td>'+elem+'</td>';
    }
    html += '</tr>';
    //$('#stages tbody tr[data-stage]').append(html);
    $(html).insertBefore('#stages tbody tr.prefooter:last');
}
 
function addMaterialToStage2(){
    if ( !$('#materials option:selected').length || $('#materials option:selected').val() === '' ){
        return;
    }
    var material = $('#materials option:selected').attr('data-material');
    if ( material === '' ){
        return;
    }
    material = $.parseJSON(material);
    //console.log(material);
     var valu = $("#selstage").val();
    stage_length++;
    var currentStageLength2 = $('input[data-key="count"][data-stage="' + valu + '"]').last().val();
    currentStageLength2++;
    var html = '<tr>';
    for(var i = 0; i < material_body_row.length; i++){
        var key = material_body_row[i];
        var elem = '';
        if ( key == 'Delete' ){
            elem = '<input class="btn btn-danger delete-material-btn" type="button" value="X" />';
        } else{
            var type = $.inArray(key, text_keys) != -1 ? 'text' : 'number';
            var readonly = key == 'count' ? 'readonly' : '';
            value = key == 'count' ? currentStageLength2 : material[key];
            if ( value === undefined || value === null ){
                value = '';
            }
            if (key === 'unit') {
               // value = '<select><option>Kg</option><option>g</option><option>L</option><option>ml</option></select>';
              //$("#stages > tbody > tr:nth-child(2) > td:nth-child(7)").append("<td><select class='form-control'><option>Kg</option><option>g</option><option>L</option><option>ml</option></select></td>");
            //elem = '  <input type="text" class="form-control" list="browsers"><datalist id="browsers"><option value="Kg"><option value="L"></datalist>';
            //elem = '<select class="form-control"><option>Kg</option><option>g</option><option>L</option><option>ml</option></select>';
            //elem = '<input class="form-control" type="text" placeholder="Kg or L">'
            elem = '<input placeholder="Select" type="'+type+'" '+readonly+''+input_props+' data-stage_length="'+currentStageLength2+'" data-stage="'+valu+'" data-key="'+key+'" value="'+value+'" list="units"><datalist id="units"><option value="Kg"><option value="g"><option value="L"><option value="ml"></datalist>';

            }else
            {
               elem = '<input type="'+type+'" '+readonly+''+input_props+' data-stage_length="'+currentStageLength2+'" data-stage="'+valu+'" data-key="'+key+'" value="'+value+'" />';

            }
        }
        //console.log(key, value);
        html += '<td>'+elem+'</td>';
    }
    html += '</tr>';
    //$('#stages tbody tr[data-stage]').append(html);
    //$("#stages > tbody > tr.prefooter").eq(stage-1).append(html);
    //$(html).insertBefore('#stages tr.prefooter:last');
   
    var ro = $('tr.footer[data-stage='+valu+']').prev();
    $(html).insertBefore(ro);
    //$(html).insertBefore('#stages tbody tr.prefooter:last');
}

function addmaterialeachstage(){

    if ( !$('#materials option:selected').length || $('#materials option:selected').val() === '' ){
        return;
    }
    var material = $('#materials option:selected').attr('data-material');
    if ( material === '' ){
        return;
    }
    material = $.parseJSON(material);
    //console.log(material);
    stage_length++;
    var html = '<tr>';
    for(var i = 0; i < material_body_row.length; i++){
        var key = material_body_row[i];
        var elem = '';
        if ( key == 'Delete' ){
            elem = '<input class="btn btn-danger delete-material-btn" type="button" value="X" />';
        } else{
            var type = $.inArray(key, text_keys) != -1 ? 'text' : 'number';
            var readonly = key == 'count' ? 'readonly' : '';
            value = key == 'count' ? stage_length : material[key];
            if ( value === undefined || value === null ){
                value = '';
            }
            if (key === 'unit') {
               // value = '<select><option>Kg</option><option>g</option><option>L</option><option>ml</option></select>';
              //$("#stages > tbody > tr:nth-child(2) > td:nth-child(7)").append("<td><select class='form-control'><option>Kg</option><option>g</option><option>L</option><option>ml</option></select></td>");
            //elem = '  <input type="text" class="form-control" list="browsers"><datalist id="browsers"><option value="Kg"><option value="L"></datalist>';
            //elem = '<select class="form-control"><option>Kg</option><option>g</option><option>L</option><option>ml</option></select>';
            //elem = '<input class="form-control" type="text" placeholder="Kg or L">'
            elem = '<input placeholder="Select" type="'+type+'" '+readonly+''+input_props+' data-stage_length="'+stage_length+'" data-stage="'+stage+'" data-key="'+key+'" value="'+value+'" list="units"><datalist id="units"><option value="Kg"><option value="g"><option value="L"><option value="ml"></datalist>';

            }else
            {
               elem = '<input type="'+type+'" '+readonly+''+input_props+' data-stage_length="'+stage_length+'" data-stage="'+stage+'" data-key="'+key+'" value="'+value+'" />';

            }
        }
        //console.log(key, value);
        html += '<td>'+elem+'</td>';
    }
    html += '</tr>';
    //$('#stages tbody tr[data-stage]').append(html);
    //$("#stages > tbody > tr.prefooter").eq(stage-1).append(html);
    $(html).insertBefore('#stages tr.prefooter:last');
}

function addStage(){
    if ( stage <= 0 || stage > 5 || stage_length > 10 ){
        alert('Άκυρη επιλογή');
    } else{
        if ( stage_length == 0 ){
            alert('Εισάγετε στοιχεία στο προηγούμενο στάδιο');
        } else{
            if ( checkFooterOfPreviousStageIsDone() === false ){
                alert('Συμπληρώστε τα πεδία του αποτελέσματος');
            } else{
                stage++;
                stage_length = 1;
                var stage_text = '', pre_footer_text = '', footer_text = '';
                var prev_stage_text = '<tr data-stage="'+stage+'">';
                //var a = '<td><input type="button" id="add-mat-btn" class="btn btn-primary" value="+"/></td>';
                var $element_to_clone = $('tr.footer[data-stage='+(stage-1)+'] input');
                //console.log($element_to_clone)
                var length = $element_to_clone.length;
                $element_to_clone.each(function(iteration){
                    var value = $(this).val();
                    var key = $(this).attr('data-key');
                    //RENAME KEYS FROM FOOTER TO BODY
                    if ( key == 'output' ){
                        key = 'name';
                    } else if ( key == 'usd' ){
                        key = 'unit';
                       // value = '';
                    } else if ( key == 'w/w yield' ){
                        key = 'cc/kg input';
                        //value = '';
                    } else if ( key == 'qty out' ){
                        key = 'qty';
                    } else if ( key == 'api cc' ){
                        key = 'cc/kg api';
                        value = '';
                    } else if ( key == 'cc/kg output' ){
                        value = '';
                    } else if ( key == 'actual cc/kg api' ){
                        value = '';
                    } else if ( key == 'cost' ){
                        //value = '';
                    } else if ( key == 'mol' ){
                        //value = '';
                    } else if ( key == 'mol yield' ){
                        key = 'mol ratio';
                        value = '';
                    } else if ( key == 'contribution' ){
                        value = '';
                    } else if ( key == 'eur' ){
                        //console.log(value);
                        key = 'price/kg';
                    }
                    if ( key === undefined || key === '' ){
                        if ( iteration == 0 ){
                            //prev_stage_text += '<td><input type="number" value="1" readonly /></td>';
                            prev_stage_text += '<td><input type="number" readonly="" step="0.01" min="0" max="9999" maxlength="50" class="form-control" data-stage_length="1" data-stage="'+stage+'" data-key="count" value="1"></td>';
                        } else if ( iteration == length - 1 ){
                            //prev_stage_text += '<td><input class="btn btn-danger delete-material-btn" type="button" value="X" /></td>';
                           prev_stage_text += '<td><input class="form-control" data-key="" readonly="" type="number"></td>';
                        } else{
                            prev_stage_text += '<td></td>';
                        }
                    } else{
                        var type = $.inArray(key, text_keys) != -1 ? 'text' : 'number';
                        if (key === 'unit') {
                            prev_stage_text += '<td><input placeholder="Select" type="'+type+'" '+readonly+''+input_props+' data-stage_length="'+stage_length+'" data-stage="'+stage+'" data-key="'+key+'"  list="units"><datalist id="units"><option value="Kg"><option value="g"><option value="L"><option value="ml"></datalist></td>';
                        }
                        else
                        {
                            prev_stage_text += '<td><input type="'+type+'" data-stage_length="'+stage_length+'" data-stage="'+stage+'" data-key="'+key+'" value="'+value+'" '+input_props+' /></td>';

                        }
                    }
                });
                prev_stage_text += '</tr>';
                if ( !$('#stages tbody tr:eq('+(stage-1)+') td.stage-text').length ){
                    var cells_length = $('#stages thead th').length;
                    //BODY
                    stage_text += '<tr data-stage="'+stage+'"><td class="stage-text" colspan="' + cells_length + '">Stage ' + stage + '</td></tr>';
                    var stage_footer_row = ['', 'Output', '', 'Density', 'MW', 'EUR', 'USD', 'qty out', 'w/w yield', 'cc/kg OUTPUT', 'API cc', 'actual cc/kg API', 'cost', 'mol', 'mol yield', 'contribution', ''];
                    pre_footer_text = '<tr class="prefooter">';
                    footer_text = '<tr data-stage="'+stage+'" class="footer">';
                    for(var i = 0; i < stage_footer_row.length; i++){
                        var low_key = stage_footer_row[i].toLowerCase();
                        //PREFOOTER
                        pre_footer_text += '<td data-text="'+low_key+'">' + stage_footer_row[i] + '</td>';
                        var readonly = !stage_footer_row[i].length || stage_footer_row[i] == 'EUR' || stage_footer_row[i] == 'USD' ? ' readonly ': '';
                        
                        var type = low_key == 'output' ? 'text' : 'number';
                        //FOOTER
                        footer_text += '<td><input class="form-control" data-key="'+low_key+'" '+readonly+' type="'+type+'" /></td>';
                    }
                    pre_footer_text += '</tr>';
                    footer_text += '</tr>';
                    
                }
                //ADD STAGE FOOTER TEXT
                $('#stages tbody').append(stage_text + '' + prev_stage_text + '' + pre_footer_text + '' + footer_text);
            }
        }
    }
}


function saveNewMaterial(){
    //console.log(is_complete, is_saved);
    if ( is_complete === false ){
        alert('Πρέπει πρώτα να ολοκληρώσετε τη διαδικασία');
        return;
    }
    if ( is_saved === true ){
        alert('Έχετε πραγματοποιήσει κάνει αποθήκευση');
        return;
    }
    var $new_element = $('#stages tbody tr.footer:last');
    if ( !$new_element.length ){
        return;
    }
    var map = {'name': 'output', 'density': 'density', 'mw': 'mw', 'price': 'eur'};
    var data = {};
    for(key in map){
        data[key] = $new_element.find('input[data-key='+map[key]+']').val();
    }
    //console.log(data);
    $.ajax({
        data: data,
        type: 'post',
        url: 'save-material.php',
        complete: function(res){
            var response = res.responseText;
            //console.log(response);
            if ( response !== '' ){
                var json = $.parseJSON(response);
                if ( json.type == 'error' ){
                    toastr.error(json.message);
                } else if ( json.type == 'success' ){
                    toastr.success(json.message);
                    is_saved = true;
                }
            }
        }
    });
}


function getInputs(){
    $('#stages tbody tr:not(.footer) td input:not([readonly]):not([type="button"])').each(function(){
        var Stage = $(this).attr('data-stage');
        var Stage_length = $(this).attr('data-stage_length');
        var key = $(this).attr('data-key');
        var val = $(this).val();
        //console.log(Stage, Stage_length, key, val);
        //console.log($(this));
        //console.log('stage_'+Stage, 'stage_length_'+Stage_length, key, val);
        inputs['body']['stage_'+Stage]['stage_length_'+Stage_length][key] = val;
    });
    $('#stages tbody tr.footer td input:not([readonly]):not([type="button"])').each(function(){
        var stage = $(this).closest('tr.footer').attr('data-stage');
        var key = $(this).attr('data-key');
        var val = $(this).val();
        inputs['footer_'+stage][key] = val;
    });
    //console.log(inputs);
    return inputs;
}


function checkFooterOfPreviousStageIsDone(){
    var done = true;
    var exclude_keys = ['api cc', 'actual cc/kg api', 'cost', 'contribution', 'eur', 'usd', 'mol', 'mol yield'];
    $('tr.footer[data-stage='+stage+'] input:not([readonly]):not([type="button"])').each(function(){
        var key = $(this).attr('data-key');
        if ( $.inArray(key, exclude_keys) != -1 ){
            //CONTINUE
            return true;
        }
        var val = $(this).val();
        if ( val === '' ){
            console.log('Λείπει η τιμή του: '+key);
            done = false;
            return false;
        }
    });
    return done;
}


function deleteMaterialFromStage($this){
    //if ( stage_length == 0 ){
      //  return;
    //}
    var $parent = $this.closest('tr');
    if ( $parent.length ){
        $parent.remove();
        stage_length--;
        initializeVariables();
        currentStageLength--;
        fixMaterialsIncrementNumbers();
    }
}

function fixMaterialsIncrementNumbers(){
    for(var i = 1; i <= stage; i++){
        $('#stages tbody tr[data-stage="'+i+'"]:not(.footer) td input[data-key="count"]').each(function(iteration){
            $(this).val((iteration + 1));
        });
    }
}


//FILL MATERIAL DATA
function fillWithMaterialData(value, material_data){
    //console.log(value, material_data);
    if ( material_data !== '' ){
        $('.delete-btn, #material-price').show();
        var data = $.parseJSON(material_data);
        //console.log(data);
        for(field in data){
            $('.center-wrap input[name="'+field+'"]').val(data[field]);
        }
        if ( window.location.href.indexOf('material_price') != -1 ){
            var dateParam = getUrlParameter(window.location.href, 'date');
            window.location.href = 'material_price.php?mid=' + data.id + '&date=' + dateParam;
        }
    }
}


//DATERANGEPICKER
function initDateRangePicker(){
    if ( $('#chart').length ){
        $('#daterangepicker').daterangepicker({
            "locale": {
                "format": "DD/MM/YYYY",
            }
        }, function(start, end, label){
            getSelectedMaterialPrices(start, end);
        });
    }
}


function getSelectedMaterialPrices(start, end){
    var material_name = $('#materials-chart option:selected').text();
    var material_prices = $('#materials-chart option:selected').attr('data-prices');
    if ( material_prices !== undefined && material_prices !== '' ){
        material_prices = $.parseJSON(material_prices);
        if ( material_prices.length ){
            displayChart(material_name, material_prices, start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
        }
    }
}


//CHARTS
function displayChart(material_name, material_prices, start_date, end_date){
    //console.log(material_name, material_prices);
    var prices = [];
    var dates = [];
    for(var i = 0; i < material_prices.length; i++){
        var date = moment(material_prices[i].date);
        //console.log(date.format('DD/MM/YYYY'));
        var price = material_prices[i].price;
        var current_date = moment(date, 'YYYY-MM-DD');
        var start = moment(start_date, 'YYYY-MM-DD');
        var end = moment(end_date, 'YYYY-MM-DD')
        if ( current_date.isBetween(start, end) || current_date.isSame(start) || current_date.isSame(end) ){
            dates.push(date.format('DD/MM/YYYY'));
            prices.push(parseFloat(price));
        }
    }
    //console.log(prices);
    var ctx = document.getElementById('chart');
    if ( myChart !== null ){
        myChart.destroy();
    }
    myChart = new Chart(ctx, {
        maintainAspectRatio: false,
        responsive: true,
        type: 'line',
        data: {
            labels: dates,
            datasets:  [{ 
                data: prices,
                borderColor:  dynamicColors(),
                //fill: false
            }],
        },
        options: {
            legend: {
                display: false
            },
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                yAxes: [{
                ticks: {
                    callback: function(value, index, values) {
                        return value + ' €';
                    }
                }
                }]
            }
        }
    });
    
}

//DATEPICKER
function initDatePicker(){
    if ( $('#datepicker').length ){
        $('#datepicker').datepicker({
            format: 'yyyy/mm/dd',
            weekStart: 1
        }).on('changeDate', function(e) {
            var date = moment(e.date).format('YYYY-MM-DD');
            var dateParam = getUrlParameter(window.location.href, 'date');
            $('#selected-date').val(date);
            if ( $('#material-select').val() !== '' ){
                $('#material-price').show();
            } else{
                $('#material-price').hide();
            }
            if ( $('#material-select').val() !== '' && dateParam != date ){
                var selected_material = $('#material-select').val();
                window.location.href = 'material_price.php?mid=' + selected_material + '&date=' + date;
            }
        });
        var dateParam = getUrlParameter(window.location.href, 'date');
        //console.log(dateParam);
        if ( dateParam === '' ){
            $('#datepicker').datepicker('setDate', 'now');
        } else{
            $('#datepicker').datepicker('setDate', dateParam);
        }
    }
}


function getUrlParameter(url, sParam){
    var sPageURL = decodeURIComponent(url.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}


function calculateUSDFromEUR(eur){
    var eur_usd_parity = 0;
    if ( $('#parity').length ){
        eur_usd_parity = parseFloat($('#parity').val());
    }
    return parseFloat(eur * eur_usd_parity).toFixed(2);
}


function randomIntegerRange(max){
    var minimum = 1;
    var maximum = max ? max : 999;
    return Math.floor(Math.random() * (maximum - minimum + 1)) + minimum;
}

function initializeVariables() {
    stage = $('td.stage-text:last').parent().attr("data-stage");
    stage_length=$("#stages > tbody > tr:nth-last-child(3) > td:nth-child(1) > input").val();
}

function dynamicColors() {
    var r = Math.floor(Math.random() * 255);
    var g = Math.floor(Math.random() * 255);
    var b = Math.floor(Math.random() * 255);
    return "rgba(" + r + "," + g + "," + b + ", 0.5)";
}