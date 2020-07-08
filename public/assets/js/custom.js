



$.blockUI.defaults = {
    message: '<div class="spinner-border text-dark mr-2" role="status"><span class="sr-only">Loading...</span></div>',
    centerX: true,
    centerY: true,
    fadeIn: 0,
    css: {
        padding: 0,
        margin: 0,
        width: '30%',
        top: '40%',
        left: '35%',
        textAlign: 'center',
        color: '#000',
        border: '0px solid #aaa',
        backgroundColor: 'transparent',
        cursor: 'wait'
    },
    themedCSS: {
        width: '30%',
        top: '40%',
        left: '35%'
    },
    overlayCSS: {
        backgroundColor: '#ccc',
        opacity: 0.3,
        cursor: 'wait'
    },
    baseZ: 1051
};
$(document).ajaxStart($.blockUI).ajaxStop($.unblockUI);

$.extend(true, $.fn.dataTable.defaults, {
    language: {
        url: "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/French.json"
    }
});



//$.blockUI();

jQuery.validator.addMethod("avanceMinPrix", function (value, element) {
    return value > $(element).closest('form').find('[name=prix]').val();
}, "Avance ne peut pas être superieur à prix");


function searchDates(e, type) {
    var date1 = $(e).closest('form').find('input[name=date1]').val();
    var date2 = $(e).closest('form').find('input[name=date2]').val();

    if (type == "c") {
        $.ajax({
            url: "/api/consultations",
            type: "POST",
            data: { "date1": date1, "date2": date2 },
            success: function (d) {
                if (d != null && d.hasOwnProperty('content') && d.content.includes("<table")) {
                    $('.data-table').closest('.card-body').html(d.content);
                }

            }
        })

    } else {
        $.ajax({
            url: "/api/prestations",
            type: "POST",
            data: { "date1": date1, "date2": date2 },
            success: function (d) {
                if (d != null && d.hasOwnProperty('content') && d.content.includes("<table")) {
                    $('.data-table').closest('.card-body').html(d.content);
                }
            }
        })
    }
}


function doctorChanged(e) {
    if (e.value == -1) {
        $('.new-medecin [name=nommedecin] , .new-medecin [name=prenommedecin]').removeAttr('disabled');
    }
    else {
        $('.new-medecin [name=nommedecin] , .new-medecin [name=prenommedecin]').attr('disabled', '');
    }

    $('.form-medecin').valid();
}

function changeState(e, id, type) {

    var dropdown = $(e).closest('.btn-group');
    var etat = 1;
    if ($(e).hasClass('etat0')) {
        //alert(1);
        dropdown.find('.btn').html("En cours");
        dropdown.find('.btn').removeClass('btn-success');
        dropdown.find('.btn').addClass('btn-warning');
        $(e).removeClass('etat0');
        $(e).addClass('etat1');
        $(e).html('Términé');
        etat = 0;
    } else if ($(e).hasClass('etat1')) {
        //alert(2);
        dropdown.find('.btn').html("Terminé");
        dropdown.find('.btn').removeClass('btn-warning');
        dropdown.find('.btn').addClass('btn-success');
        $(e).removeClass('etat1');
        $(e).addClass('etat0');
        $(e).html('En cours');
        etat = 1;
    }


    if ("p" == type) {
        $.ajax({
            url: "/api/prestation/change_state",
            type: "POST",
            data: { "etat": etat, "id": id },
            success: function (data) {
                if (data.hasOwnProperty('success') && data.success == true) {
                    toastr.success("Tout été bien enregistré", "Modif");
                } else {
                    toastr.error("Erreur", "Modif");
                }
            }
        });
    }
    else if ("c" == type) {
        $.ajax({
            url: "/api/consultation/change_state",
            type: "POST",
            data: { "etat": etat, "id": id },
            success: function (data) {
                if (data.hasOwnProperty('success') && data.success == true) {
                    toastr.success("Tout été bien enregistré", "Modif");
                } else {
                    toastr.error("Erreur", "Modif");
                }
            }
        });
    }

}


function openPrestation(id, tab) {
    $.ajax({
        url: "/api/prestation/get",
        type: "POST",
        data: { "p": id, "tab": tab },
        success: function (d) {
            if (d != null && d.hasOwnProperty('content')) {
                $('#general-modal .modal-body').html(d.content);
                $('#general-modal .modal-title').html("Details prestation");
                $('#general-modal').modal('show');
            }
        }
    })
}

function calculerPaiementDossier(e) {

    var tr = $(e).closest('tr');

    if (e.value != "") {

        if (isNaN(e.value))
            $(e).val(e.value.slice(0, -1));


        var montant = tr.find('.montant');

        if (parseFloat(e.value) > parseFloat(montant.text()) && parseFloat(montant.text()) > 0) {

            $(e).val(e.value.slice(0, -1));
        }

        tr.find('.statutpaiement').removeClass('text-success');
        tr.find('.statutpaiement').removeClass('text-warning');
        tr.find('.statutpaiement').removeClass('text-danger');

        $(e).removeClass('is-invalid');
        $(e).addClass('is-valid');
        tr.find('.reste').text((parseFloat(montant.text()) - parseFloat(e.value)).toFixed(2));

        if (parseFloat(montant.text()) - parseFloat(e.value) == 0) {
            tr.find('.statutpaiement').addClass('text-success');
        }
        else if (parseFloat(montant.text()) > parseFloat(e.value) && parseFloat(e.value) != 0) {
            tr.find('.statutpaiement').addClass('text-warning');
        }
        else {
            e.value = 0;
            tr.find('.statutpaiement').addClass('text-danger');
        }

        tr.find('.avancespn').text(e.value);

    }
    else {
        $(e).removeClass('is-invalid');
        $(e).removeClass('is-valid');
    }
}


function savePaiementDossier(e, idcon) {
    var tr = $(e).closest('tr');
    var avance = tr.find('.avancespan').val();
    var reste = tr.find('.reste').text();
    var id = tr.attr('data-child-value');
    var total = tr.find('.montant').text();

    $.ajax({
        url: "/api/consultation/pay",
        type: "POST",
        data: { "avance": avance, "reste": reste, "id": id, "idcon": idcon, "total": total },
        success: function (data) {
            if (data != null && data.hasOwnProperty('success')) {
                if (data.success) {
                    toastr.success(data.message, "Success");
                    openConsultation($('.consultation').val(), 4);
                    searchDates($('.form-search-consultation').get(0), 'c');
                }
                else {
                    toastr.error(data.message, "Erreur");
                }
            }
        }
    })
}


function deAffecterExamen(id) {
    if (confirm('Etes vous sur de vouloir cette action ?')) {
        if (!isNaN(id)) {
            $.ajax({
                url: "/api/prestations/delete",
                type: "POST",
                data: { "id": id },
                success: function (data) {
                    if (data != null && data.hasOwnProperty('success')) {
                        if (data.success) {
                            toastr.success(data.message, "Success");
                            openConsultation($('.consultation').val(), 2);
                        }
                        else {
                            toastr.error(data.message, "Erreur");
                        }
                    }
                    else {
                        toastr.error("Erreur interne Contactez le support", "Erreur");
                    }

                }

            });

        }
    }

}

function openConsultation(id, tab = 1) {
    $('.data-table').DataTable().rows().eq(0).each(function (idx) {
        var row = $('.data-table').DataTable().row(idx);
        if (row.child.isShown()) {
            row.child.hide();
        }
    });

    $.ajax({
        url: "/api/consultation/get",
        type: "POST",
        data: { "c": id, 'tab': tab },
        success: function (d) {
            if (d != null && d.hasOwnProperty('content')) {
                $('#general-modal .modal-body').html(d.content);
                $('#general-modal .modal-title').html("Details consultation");
                $('#general-modal').modal('show');
            }
        }
    })
}

function calculerPrestationPaiement(e) {
    var form = $(e).closest('form');

    if (e.value != "") {

        if (isNaN(e.value))
            $(e).val(e.value.slice(0, -1));


        var montant = form.find('.montant');

        if ((parseFloat(e.value) > parseFloat(montant.val())) && parseFloat(montant.val()) > 0) {
            $(e).val(e.value.slice(0, -1));
        }

        form.find('.statutpaiement').removeClass('text-success');
        form.find('.statutpaiement').removeClass('text-warning');
        form.find('.statutpaiement').removeClass('text-danger');

        $(e).removeClass('is-invalid');
        $(e).addClass('is-valid');
        form.find('.reste').val((parseFloat(montant.val()) - parseFloat(e.value)).toFixed(2));

        if (parseFloat(montant.val()) - parseFloat(e.value) == 0) {
            form.find('.statutpaiement').addClass('text-success');
        } else if (parseFloat(montant.val()) > parseFloat(e.value) && parseFloat(e.value) != 0) {
            form.find('.statutpaiement').addClass('text-warning');
        } else {
            form.find('.statutpaiement').addClass('text-danger');
        }

        form.find('.avancespn').val(e.value);

    } else {
        $(e).removeClass('is-invalid');
        $(e).removeClass('is-valid');
    }
}

function showUpdate(type, id) {
    $.ajax({
        url: "/admin/update/get",
        type: "GET",
        data: { "type": type, "id": id },
        success: function (d) {
            if (d != null && d.hasOwnProperty('content')) {
                $('#general-modal .modal-body').html(d.content);
                $('#general-modal .modal-title').html("Modifier les informations");
                $('#general-modal').modal('show');
            }
        }
    })
}

function showAdd(type) {
    showUpdate(type, 0);
}


function savePrestationPaiement(e, idprest) {
    var form = $(e).closest('form');
    var avance = form.find('.avancespan').val();
    var reste = form.find('.reste').val();
    var id = idprest;
    var total = form.find('.montant').val();

    var date1 = $('form input[name=date1]').val();
    var date2 = $('form input[name=date2]').val();

    $.ajax({
        url: "/api/prestation/pay",
        type: "POST",
        data: { "avance": avance, "reste": reste, "id": id, "total": total },
        success: function (data) {
            if (data.hasOwnProperty('success') && data.success == true) {
                toastr.success("Tout été bien enregistré", "Modif");
                openPrestation(id, 1);
                if(!(/dossier-patients/.test(window.location.pathname)))
                        searchDates($('.form-search-prestation').get(0), 'p');

            } else {
                toastr.error("Erreur", "Modif");
            }
        }
    })
}

function print(e,header=null) {

    if(header  != null)
        header = $(header).html();

    $(e).kinziPrint({
        importStyles: true,
        header: header,
        footer: '<h1 class="text-center">Hopital de Marrakech</h1>',
        loadCSS: 'assets/css/light.css'
    });

}

function generateFacturePrestation(id) {
    $.ajax({
        url: "/api/facture/generate",
        type: "POST",
        data: { "id": id },
        success: function (data) {
            if (data.hasOwnProperty('success')) {
                toastr.success("Facture Générée", "Facture");
                openPrestation(id, 3);
            }
        }
    })
}

function initDatePickers() {
    if ($('div.date').length > 0) {
        $('div.date').each(function () {
            $(this).attr('id', parseInt(Math.random() * 10000));
            $(this).find('input').attr('data-target', '#' + $(this).attr('id'));
            $(this).find('input').addClass('datetimepicker-input');
            $(this).find('.input-group-append').attr('data-target', '#' + $(this).attr('id'));
        });

        $('.datetimepicker-date').datetimepicker({ format: 'D-M-Y', locale: 'fr' });

        $('.dtaelong').datetimepicker({ format: 'DD-MM-YYYY HH:mm:ss', locale: 'fr' });

        $('.date:not(.dtaelong)').datetimepicker({ format: 'DD-MM-YYYY', locale: 'fr' });
    }
}

function addFormAffectation() {
    $('.form-affectation-prestation').toggle(1000);
}


function patientSelect2() {
    $(".searchpatient").select2({
        placeholder: "Choisissez la valeur",
        language: {
            noResults: function () {
                return '<button class="btn btn-primary w-100" onclick="showAddPatientModal();">Nouveau patient</a>';
            },
        },
        escapeMarkup: function (markup) {
            return markup;
        },
        closeOnSelect: true
    });
}

function patientComboChanged(e) {
    $.ajax({
        url: "/api/patient/getForm",
        type: "GET",
        data: { "id": e.value },
        success: function (d) {
            if (d != null && d.hasOwnProperty('content')) {
                $('.patientcontainer').html(d.content);
                initDatePickers();
                patientSelect2();
                savePatient(function () {
                    dataform += "&patientid=" + $('select[name=patientid]').val() + "&";
                    dataform += $('.form-patient').serialize();
                });
                $('.form-patient').valid();
            }
        }
    })
}

function parenteComboChanged(e) {
    var parente = e.value;
    if (parente != -1) {
        $('input[name=nomadh],input[name=prenomadh],input[name=cinadh],input[name=nMutu]').removeAttr('disabled');
    }
    else {
        $('input[name=nomadh],input[name=prenomadh],input[name=cinadh],input[name=nMutu]').val('');
        $('input[name=nomadh],input[name=prenomadh],input[name=cinadh],input[name=nMutu]').attr('disabled', 'disabled');
    }
    $('.form-mutuelle').valid();
}


function validateForm(formselector, submitHandler, rules) {
    $(formselector).validate({
        submitHandler: submitHandler,
        focusInvalid: true,
        rules: rules,
        // Errors
        errorPlacement: function errorPlacement(error, element) {
            var $parent = $(element).parents(".form-group");
            // Do not duplicate errors
            if ($parent.find(".jquery-validation-error").length) {
                return;
            }
            $parent.append(
                error.addClass("jquery-validation-error small form-text invalid-feedback")
            );
        },
        highlight: function (element) {
            var $el = $(element);
            var $parent = $el.parents(".form-group");
            $el.addClass("is-invalid");
            // Select2 and Tagsinput
            if ($el.hasClass("select2-hidden-accessible") || $el.attr("data-role") === "tagsinput") {
                $el.parent().addClass("is-invalid");
            }
        },
        unhighlight: function (element) {
            $(element).parents(".form-group").find(".is-invalid").removeClass("is-invalid");
        }
    });
}

function savePatient(handler, selector = ".form-patient") {
    $(selector).validate({
        submitHandler: handler,
        focusInvalid: true,
        rules: {
            "form": {
                required: true
            },
            "form[ville]": {
                required: true
            },
            "form[mutuelle]": {
                required: true
            },
            "form[date_naiss]": {
                required: true
            }
        },
        // Errors
        errorPlacement: function errorPlacement(error, element) {
            var $parent = $(element).parents(".form-group");
            // Do not duplicate errors
            if ($parent.find(".jquery-validation-error").length) {
                return;
            }
            $parent.append(
                error.addClass("jquery-validation-error small form-text invalid-feedback")
            );
        },
        highlight: function (element) {
            var $el = $(element);
            var $parent = $el.parents(".form-group");
            $el.addClass("is-invalid");
            // Select2 and Tagsinput
            if ($el.hasClass("select2-hidden-accessible") || $el.attr("data-role") === "tagsinput") {
                $el.parent().addClass("is-invalid");
            }
        },
        unhighlight: function (element) {
            $(element).parents(".form-group").find(".is-invalid").removeClass("is-invalid");
        }
    });
}

function saveConsultation() {

    $(".form-patient").submit();
    $(".form-medecin").submit();
    $(".form-examen").submit();
    $(".form-mutuelle").submit();

    // if(!$(".form-patient").valid()) $(".form-patient").submit();
    // if(!$(".form-medecin").valid()) $(".form-medecin").submit();
    // if(!$(".form-examen").valid()) $(".form-examen").submit();
    // if(!$(".form-mutuelle").valid()) $(".form-mutuelle").submit();

    if ($(".form-patient").valid() && $(".form-medecin").valid() && $(".form-examen").valid() && $(".form-mutuelle").valid()) {
        $.ajax({
            url: "/api/consultations/add",
            type: "POST",
            data: dataform,
            success: function (data) {
                if (data.hasOwnProperty('success')) {
                    if (data.success) {
                        toastr.success(data.message, "Consultation");
                    }
                    else {
                        toastr.error(data.message, "Erreur");
                    }
                }
            }
        });
    }
}


function comboboxNatureChanged(e, targetClass) {
    var nature = e.value;
    targetClass = $(e).closest('form').find(targetClass);
    $.ajax({
        url: "/nature/examens/get",
        type: "POST",
        data: { "nature": nature },
        success: function (data) {
            if (data.hasOwnProperty('data')) {
                $(targetClass).html(null);
                var d = data.data;
                d.forEach(function (item, index) {
                    var data1 = {
                        id: item.id,
                        text: item.nom
                    };
                    var newOption = new Option(data1.text, data1.id, true, true);
                    $(targetClass).append(newOption).trigger('change');
                })

                //if($(targetClass).data('select2'))

            }
            return $('.form-examen').length > 0 ? $('.form-examen').valid() : null;
        }
    });
}

function comboboxPrestationChanged(e, targetClass) {
    var examen = e.value;
    targetClass = $(e).closest('form').find(targetClass);
    $.ajax({
        url: "/examens/prix/get",
        type: "POST",
        data: { "examen": examen },
        success: function (data) {
            if (data.hasOwnProperty('prix')) {
                $(targetClass).val('');
                var d = data.prix;
                $(targetClass).val(d);
            }
            return $('.form-examen').length > 0 ? $('.form-examen').valid() : null;
        }
    });
}


function afficherfacture(id) {
    $('#facure-editor').html("");

    var facureEditor = document.getElementById('facure-editor');

    $.ajax({
        url: "/api/facture/show",
        type: "POST",
        data: { "id": id },
        success: function (data) {
            if (!isNaN(id) && data.hasOwnProperty('content')) {

                $('#facure-editor').css('height', '70vh');
                $('#facure-editor').html(data.content);



                if (sceditor.instance(facureEditor) != null)
                    sceditor.instance(facureEditor).destroy();

                sceditor.create(facureEditor, {
                    format: 'xhtml',
                    icons: 'monocons',
                    style: '/assets/css/light.css',
                    locale: 'fr',
                    emoticonsRoot: '/sceditor/',
                    plugins: 'autosave,undo',
                    autoUpdate: true,
                    autosave: {
                        expires: 86400000
                    }
                });
            }
            else {

            }
        }
    })
}




function genererFactures(e) {
    var checks = $(e).closest('.row').find('input.custom-control-input');
    var ids = [];
    //console.log(checks);

    $('.row input.custom-control-input').each(function () {
        if ($(this).is(':checked')) {
            ids.push($(this).val());
        }

    })

    $('#example2').html("");

    var textarea2 = document.getElementById('example2');


    if (ids.length > 0) {
        $.ajax({
            url: "/api/facture-consultation/generate",
            type: "POST",
            data: { "ids": ids },
            success: function (data) {
                toastr.error(data.message, "Erreur");
            }
        })
    }
    else {


    }
}




function reloadComboPatients(selected, selector) {
    $.ajax({
        url: "/api/patients/all",
        type: "GET",
        success: function (d) {
            if (d.hasOwnProperty('data')) {
                if (d.data.length > 0) {
                    $(selector).html('');
                    d.data.forEach(function (item, index) {
                        var data = {
                            id: item.id,
                            text: item.prenom + " " + item.nom
                        };
                        if (selected == data.id) selected = -1; else selected = 0;
                        var newOption = new Option(data.text, data.id, selected == -1 ? true : false, selected == -1 ? true : false);
                        $(selector).append(newOption).trigger('change');
                    })
                }
            }
        }
    });
}



function showAddPatientModal() {
    $.ajax({
        url: "/api/patient/getForm",
        type: "GET",
        success: function (d) {
            if (d != null && d.hasOwnProperty('content')) {
                $('#general-modal .modal-body').html(d.content);
                $('#general-modal .modal-title').html("Nouveau patient");
                $('#general-modal').modal('show');
                savePatient(function () {
                    $.ajax({
                        url: "/api/patient/save",
                        type: "POST",
                        data: $('#general-modal .form-patient').serialize(),
                        success: function (data) {
                            if (data.hasOwnProperty('success')) {
                                if (data.success == true) {
                                    // alert(12);
                                    $('#general-modal').modal('hide');
                                    var id = 0;
                                    if (data.hasOwnProperty('options'))
                                        id = data.options.id;
                                    reloadComboPatients(id, '.searchpatient');
                                    searchDates($('.form-search-consultation').get(0), 'c');
                                }
                                else {
                                    // alert(13);
                                }
                            }
                        }
                    });
                }, "#general-modal .form-patient");
            }
        }
    })

}

function initSelect2() {

    $(".select2:not(.searchpatient):not(.select2-container)").each(function () {
        $(this).wrap("<div class='position-relative'></div>").select2({ placeholder: "Choisissez la valeur", dropdownParent: $(this).parent() });
    })

}


function reloadAdminContent(active_tab){
    $.ajax({
        url: "/admin/api",
        type: "GET",
        success: function (d) {
            if(d.hasOwnProperty('content'))
            {
                $('.tab-content').html(d.content);
                $('.data-table').DataTable();
                initDatePickers();
                initSelect2();
                patientSelect2();
                $('.tab-pane').removeClass('active');
                $('#'+active_tab).addClass('active');
            }
        }
    });
}

function deleteAdmin(type,id)
{
    if(confirm('Voulez-vous supprimez ?'))
    {
        $.ajax({
            url: "/admin/delete",
            type: "GET",
            data: { "type": type, "id": id },
            success: function (d) {
                if(d.hasOwnProperty('success'))
                {
                    d.success ? toastr.warning(d.message, "Suppression"):toastr.error(d.message, "Suppression");
                    reloadAdminContent($('.tab-pane.active').attr('id'));
                }
            }
        })
    }
}

function saveAffectation() {
    let examens = [];
    $('.examens-affectation table tbody tr').each(function () {
            if($(this).find('input[type=checkbox]').is(':checked'))
            {
                examens.push($(this).find('input[type=checkbox]').attr('name'));
            }
    })

    let nature = $('.nature-select').val();

    $.ajax({
        url: "/admin/affectation/save",
        type: "POST",
        data: { "nature": nature, "examens": examens },
        success: function (d) {
            if(d.hasOwnProperty('success'))
            {
                d.success ? toastr.success(d.message, "Affectation"):toastr.error(d.message, "Affectation");
                reloadAdminContent($('.tab-pane.active').attr('id'));
            }
        }
    })

}




function  changeNatureAffectation(value) {
    $.ajax({
        url: "/admin/affectation/set",
        type: "POST",
        data: { "nature": value },
        success: function (d) {
            if(d.hasOwnProperty('content'))
                $('.examens-affectation').html(d.content);
                $('.data-table').DataTable();
                initDatePickers();
                initSelect2();
                patientSelect2();
        }
    })
}

function   showAffectation(){
    $.ajax({
        url: "/admin/affectation/show",
        type: "GET",
        success: function (d) {
            if(d.hasOwnProperty('content'))
            {
                $('#general-modal .modal-body').html(d.content);
                $('#general-modal .modal-title').html("Affectation des examens");
                $('#general-modal').modal('show');
            }
        }
    })
}

function enablebutton()
{
    $('.generatefacture').removeAttr('disabled');
}


/***************Jquery Call ***********/

$('.modal').on('shown.bs.modal', function (e) {
    $('.data-table').DataTable();
    initDatePickers();
    initSelect2();
    patientSelect2();
})


$(function () {

    initSelect2();

    patientSelect2();

    toastr.options.positionClass = 'toast-top-center';
    toastr.options.closeMethod = 'fadeOut';
    toastr.options.closeDuration = 300;
    toastr.options.timeOut = 10000;
    toastr.options.closeEasing = 'swing';
    toastr.options.preventDuplicates = true;
    toastr.options.closeButton = true;
    $('.data-table').DataTable({language: {searchPlaceholder: "Chercher par Cin , nom , prenom et examen , ID patient ..."}});


    $('.data-table').DataTable();

    initDatePickers();
});
