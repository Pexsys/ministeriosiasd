Number.PAD_LEFT = 0;
Number.PAD_RIGHT = 1;
Number.PAD_BOTH = 2;

String.prototype.isEmpty = function () {
  return (this.lenght == 0) || (this.trim().length == 0);
};

String.prototype.titleCase = function () {
  const exceptions = ['a', 'e', 'o', 'de', 'da', 'do', 'um', 'uma', 'ao', 'em'];
  return this
    .toLowerCase()
    .split(' ')
    .map((word, index) => {
      if (index === 0 || !exceptions.includes(word)) return word.charAt(0).toUpperCase() + word.slice(1);
      return word;
    })
    .join(' ');
};

String.prototype.toInt = function () {
  return this.isEmpty() ? 0 : parseInt(this.replaceAll(".", ""), 10);
};

String.prototype.replaceAll = function (oldVal, newVal) {
  var str = this;
  while (str.indexOf(oldVal) > -1) str = str.replace(oldVal, newVal);
  return str;
};

if (!String.prototype.startsWith) {
  String.prototype.startsWith = function (searchString, position) {
    position = position || 0;
    return this.substr(position, searchString.length) === searchString;
  };
};

Number.prototype.toPadString = function (size, pad = '0', side = Number.PAD_LEFT) {
  if (!pad) pad = "0";
  if (!side) side = Number.PAD_LEFT;
  var str = "" + this,
    append = "",
    size = (size - str.length);
  var pad = ((pad != null) ? pad : " ");

  if (side == Number.PAD_BOTH) {
    str = str.pad((Math.floor(size / 2) + str.length), pad, String.PAD_LEFT);
    return str.pad((Math.ceil(size / 2) + str.length), pad, String.PAD_RIGHT);
  };

  while ((size -= pad.length) > 0) append += pad;

  append += pad.substr(0, (size + pad.length));

  return ((side == Number.PAD_LEFT) ? append.concat(str) : str.concat(append));
};

Array.prototype.isEmpty = function () {
  return (!this || this.length === 0);
};

Date.prototype.toFormattedDate = function () {
  return [this.getDate().toPadString(2), (this.getMonth() + 1).toPadString(2), this.getFullYear()].join('/');
};

Date.prototype.toDateTime = function () {
  return this.getFullYear() + "-" +
    (this.getMonth() + 1).toPadString(2) + "-" +
    this.getDate().toPadString(2) + " " +
    this.getHours().toPadString(2) + ":" +
    this.getMinutes().toPadString(2) + ":" +
    this.getSeconds().toPadString(2);
  // + "." +this.getMilliseconds().toPadString(3)
};

$.fn.visible = function (lVisible, ...effects) {
  if (lVisible) {
    this.show(...effects);
    if (this.hasClass("selectpicker")) this.selectpicker('show');
  } else {
    this.hide(...effects);
    if (this.hasClass("selectpicker")) this.selectpicker('hide');
  }
  return this;
};

$.fn.isEnabled = function () {
  return this.attr("disabled") == undefined || this.attr("disabled").isEmpty();
};

$.fn.hasAttr = function (attr) {
  return this.attr(attr) !== undefined && !this.attr(attr).isEmpty();
};

$.fn.enable = function (lEnable) {
  if (lEnable) {
    this.removeAttr('disabled');
    if (this.hasClass("selectpicker")) this.selectpicker('setStyle', 'disabled', 'remove');
  } else {
    this.attr('disabled', 'disabled');
    if (this.hasClass("selectpicker")) this.selectpicker('setStyle', 'disabled', 'add');
  }
  return this;
};

var jsLIB = {
  camera: null,
  currentUrl: null,
  parameters: {},
  prefix: 'PEX-DONS',
  rootDir: "",

  notification: ({ type = 'error', position = 'top-right', delay = 7000, fade = true, ...rest }) => $.toast({ ...rest, type, delay, fade }),

  dialogBox: function (options) {
    if (!!options.messagePre) options.message = `<pre class="text-wrap">${options.messagePre}</pre>`;
    if (!!options.irreverse) {
      if (!options.messagePre) options.message += `<br/><br/>`;
      options.message += `<span class="badge badge-warning">Após a confirmação, essa ação será irreversível!</span>`;
    }
    BootstrapDialog.show({
      size: BootstrapDialog.SIZE_NORMAL,
      draggable: false,
      closable: true,
      closeByBackdrop: false,
      closeByKeyboard: false,
      nl2br: false,
      spinicon: 'fas fa-spinner',
      animate: false,
      ...options,
    });
  },

  watingDialog: () => jsLIB.dialogBox({
    size: BootstrapDialog.SIZE_SMALL,
    closable: false,
    draggable: false,
    message: () => ($('<div align="center"><i class="fa fa-spinner fa-spin" style="font-size:200px"></i></div>')),
  }),

  modalWaiting: show => {
    if (!jsLIB.watingDialog.opened) {
      jsLIB.watingDialog.realize();
      jsLIB.watingDialog.getModalHeader().hide();
      jsLIB.watingDialog.getModalFooter().hide();
      jsLIB.watingDialog.getModalBody().css('background-color', '#0088cc');
      jsLIB.watingDialog.getModalBody().css('color', '#fff');
    }
    if (show) jsLIB.watingDialog.open();
    else jsLIB.watingDialog.close();
  },

  viewQRCode: qc => jsLIB.dialogBox({
    size: BootstrapDialog.SIZE_SMALL,
    type: BootstrapDialog.TYPE_DEFAULT,
    title: `<i class="fas fa-qrcode"></i> QRCode`,
    message: `<div class="d-flex justify-content-center">${qc}</div>`,
    closeByBackdrop: true,
    closeByKeyboard: true,
    onshown: function (dialogRef) {
      dialogRef.getModalFooter().css('display', 'block');
      dialogRef.getModalBody().addClass('bg-white');
    },
    buttons: [
      {
        id: 'btnInvert',
        icon: 'fas fa-paint-roller',
        label: ' Inverter',
        cssClass: 'btn-dark float-left',
        action: function (dialogRef) {
          $('#btnInvert').toggleClass('btn-dark btn-default');
          dialogRef.getModalBody().toggleClass('bg-white bg-black');
          const objs = dialogRef.getModalBody().find('div[style*="background-color: rgba(0, 0, 0, 1)"]');
          if (objs.length > 0) objs.css('background-color', ' rgba(255, 255, 255, 1)')
          else dialogRef.getModalBody().find('div[style*="background-color: rgba(255, 255, 255, 1)"]').css('background-color', ' rgba(0, 0, 0, 1)');
        }
      },
      {
        icon: 'fas fa-xmark',
        label: ' Fechar',
        cssClass: 'btn-default float-right',
        action: function (dialogRef) {
          dialogRef.close();
        }
      },
    ],
  }),

  getNavIcon: () => ($(".nav-link.active[route]").find(".nav-icon").attr('class').substr(9)),

  readCameraCode: function (options, callBackScan) {
    let scanner = null;
    const previewResize = () => {
      $("#preview").removeAttr('width').removeAttr('height');
      if (!$("#preview").attr('width')) setTimeout(previewResize, 500);
    };
    jsLIB.dialogBox({
      type: BootstrapDialog.TYPE_DEFAULT,
      title: `Leia o QRCode`,
      message: `<div><video id="preview" style="width:100%;"></video></div><div class="mt-1"><select name="cmCamera" id="cmCamera" class="selectpicker" opt-selected="sl" opt-label="name" data-live-search="false" data-container="body" data-width="100%" data-actions-box="false" data-size="2" add-none="false" data-title="Selecione a Câmera"></select></div>`,
      closeByBackdrop: true,
      closeByKeyboard: true,
      buttons: [
        {
          icon: 'fas fa-xmark',
          label: 'Fechar',
          cssClass: 'btn-default',
          action: function (dialogRef) {
            dialogRef.close();
          }
        },
      ],
      ...options,
      onhide: function (dialogRef) {
        if (!!scanner) scanner.stop();
      },
      onshown: function (dialogRef) {
        scanner = new Instascan.Scanner({
          video: document.getElementById('preview'),
          mirror: false,
        });
        const useCamera = camera => {
          if (!!scanner) scanner.stop();
          scanner.camera = camera;
          scanner.start();
          scanner.addListener('scan', content => callBackScan(content, dialogRef));
          scanner.addListener('active', () => previewResize());
        };
        Instascan.Camera.getCameras().then(cameras => {
          if (cameras.length > 0) {
            if (jsLIB.camera === null) jsLIB.camera = cameras.length - 1;
            cameras[jsLIB.camera].sl = true;
            jsLIB.populateOptions($('#cmCamera'), cameras);
            $('#cmCamera').on('change', function (e) {
              const camID = $(this).val();
              Instascan.Camera.getCameras().then(cameras => {
                jsLIB.camera = cameras.findIndex(itm => itm.id === camID);
                useCamera(cameras[jsLIB.camera]);
              });
            });
            return cameras[jsLIB.camera];
          } else {
            throw new Error('No cameras found.')
          }
        })
          .then(useCamera)
          .catch(console.error);
      },
    });
  },

  breadCrumb: function () {
    let crumb = ``;
    const array = $("aside.main-sidebar .nav-item .active");
    array.each((i, e) => {
      crumb += '<li class="breadcrumb-item';
      if (i === array.length - 1) crumb += ' active';
      crumb += `">${$(e).find("p").text()}</li>`;
    });
    $(".breadcrumb").html(crumb);
  },

  ajax: function (objParam, callback) {
    var retorno = undefined;
    $.ajax({
      async: (objParam.async !== undefined ? objParam.async : true),
      type: (objParam.type || 'POST'),
      cache: false,
      dataType: 'json',
      ...objParam,
      xhr: function () {
        const xhr = new window.XMLHttpRequest();
        xhr.addEventListener("progress", evt => {
          if (!objParam.onProgress) return;
          if (evt.lengthComputable) objParam.onProgress(evt.loaded / evt.total, evt);
          else objParam.onProgress(null);
        });
        return xhr;
      },
      success: (data, textStatus, jqxhr) => {
        if (typeof (objParam.success) == 'function') {
          objParam.success(data, jqxhr);
        } else if (objParam.success === undefined) {
          retorno = data;
        }
      },
      error: (jqxhr, textStatus, message) => {
        if (typeof (objParam.error) == 'function') {
          objParam.error(jqxhr, message);
        } else if (!!objParam.error) {
          jsLIB.notification({
            title: 'Acesso negado!',
            subtitle: 'Dados inválidos',
            body: 'Verifique os dados e tente novamente mais tarde.'
          });
        }
      },
    });
    return (!!callback ? callback(retorno) : retorno);
  },

  injection: function (element, hasUpdateVisual = true) {
    const setParentTree = e => {
      $(e).addClass('active');
      const parentTree = $(e).parent().parent();
      if (parentTree.hasClass('nav-treeview')) {
        parentTree.parent().addClass('menu-is-opening menu-open');
        setParentTree(parentTree.parent().find('a.nav-link:first'));
      }
    };
    let data = {};
    $(element.get(0).attributes).each(function () { data[this.nodeName] = this.nodeValue });
    jsLIB.ajax({
      url: `${jsLIB.rootDir}app/api/injection/`,
      data: { MethodName: 'inject', data },
      success: function (data) {
        if (!!data.blocked) {
          $('#logout').triggerHandler("click");
          return;
        } else if (!data.inject || !data.html) {
          return;
        }
        $("#innerContent").html(data.html);
        $("#innerContent .selectpicker").selectpicker('refresh');

        $("#innerContent .defaultTouchSpin").TouchSpin({
          min: 1,
          max: 1000,
          initval: 1,
          verticalbuttons: true,
          buttondown_class: "btn btn-secondary",
          buttonup_class: "btn btn-secondary",
        });
        $("[format=telefone]").inputmask(["(99) 9999-9999", "(99) 99999-9999"]);
        $("[format=placa]").inputmask("(AAA-9999|AAA-9A99)");
        $("[format=integer]").inputmask({
          alias: 'numeric',
          allowMinus: false,
          digits: 0,
          max: 9999,
        });
        $("[format=decimal]").inputmask({
          alias: 'numeric',
          groupSeparator: '.',
          autoGroup: true,
          digits: 2,
          radixPoint: ",",
          digitsOptional: true,
          allowMinus: false,
        });
        $("[format=currency]").inputmask({
          alias: 'numeric',
          groupSeparator: '.',
          autoGroup: true,
          digits: 2,
          radixPoint: ",",
          digitsOptional: false,
          allowMinus: false,
          prefix: 'R$ ',
        });
        $('#innerContent .form-control').inputmask();
        $('#innerContent [data-bootstrap-switch]').bootstrapSwitch();
        $('#innerContent .select2').select2({
          language: 'pt-BR',
          closeOnSelect: true,
        });
        $("#innerContent .touchspin").TouchSpin({
          min: 0,
          max: 1000,
          initval: 0,
          verticalbuttons: true,
          buttondown_class: "btn btn-secondary",
          buttonup_class: "btn btn-secondary",
        });

        $('#cmbChangeEvent').selectpicker('refresh');

        $('#refresh').visible(window.location.hostname === 'localhost');
        if (!hasUpdateVisual) {
          jsLIB.breadCrumb();
          return;
        };

        $('.sidebar')
          .find('a.nav-link.active')
          .removeClass('active');

        setParentTree(element);

        jsLIB.breadCrumb();

        if ($('body').get(0).clientWidth < 1024) $('body')
          .removeClass('sidebar-open')
          .addClass('sidebar-collapse')
          .addClass('sidebar-closed');

        $('aside')
          .removeClass('sidebar-focused');
      },
    });
  },

  getJSONFields: function (frm) {
    var retorno = {};
    frm.find($('[field]')).each(function () {
      retorno[$(this).attr("field")] = jsLIB.getValueFromField($(this));
    });
    return retorno;
  },

  getURIFields: function (frm, visible = ':visible') {
    let retorno = "";
    frm.find($(`[field]${visible}`)).each(function () {
      const value = jsLIB.getValueFromField($(this));
      if (value) retorno += (retorno.length == 0 ? "" : "&") + `${$(this).attr("field")}=${value}`;
    });
    return retorno;
  },

  getValueFromField: function (inputField) {
    switch (inputField.attr("type")) {
      case "radio":
      case "checkbox":
        return inputField.prop('checked') ? inputField.attr('value-on') : inputField.attr('value-off');
      case "wysiwyg":
        return tinymce.get(inputField.get(0).id).getContent();
      default:
        if (inputField.hasClass("date") && !!inputField.data()) return inputField.data('date');
        else if (inputField.hasClass("select2")) return inputField.select2('data').map(itm => itm.id);
        else return inputField.val();
    }
    return "";
  },

  resetForm: function (frm, callback) {
    frm.find($('[field]')).each(function () {
      $(this).parents('.form-group').removeClass('has-success');
      var value = '';
      if ($(this).attr('default-value') !== undefined && $(this).attr('default-value') != '') value = $(this).attr('default-value');
      switch ($(this).attr("type")) {
        case "radio":
        case "checkbox":
          $(this).prop('checked', false).trigger('change');
          break;
        case "wysiwyg":
          tinymce.get($(this).get(0).id).setContent('');
          break;
        case "text":
          if ($(this).parent().attr("datatype") == 'datetimepicker') {
            $(this).parent().data("DateTimePicker").setDate(null);
            $(this).val(value).trigger('change');
            break;
          }
        default:
          $(this).val(value).trigger('change');
          if ($(this).hasClass("selectpicker")) $(this).selectpicker('refresh');
          break;
      }
    });
    if (callback) callback();
  },

  setValueField: function (ctrl, value) {
    if (ctrl.length > 0) {
      switch (ctrl.attr("type")) {
        case "radio":
        case "checkbox":
          ctrl.prop('checked', ctrl.attr("value-on") == value).trigger('change');
          break;
        case "wysiwyg":
          tinymce.get(ctrl.get(0).id).setContent(value);
          break;
        case "text":
          if (ctrl.attr("data-inputmask-inputformat") == "dd/mm/yyyy") {
            const aux = (value || '').split('-');
            ctrl.val(`${aux[2]}/${aux[1]}/${aux[0]}`).trigger('change');
            break;
          }
          if (ctrl.parent().attr("datatype") == 'datetimepicker') {
            ctrl.parent().data("DateTimePicker").setDate(new Date(value.toInt()));
            break;
          }
        default:
          if (ctrl.hasClass("selectpicker")) ctrl.selectpicker('val', value).trigger('change');
          else ctrl.val(value).trigger('change');
      }
    }
  },

  populateForm: function (frm, data, callback) {
    jsLIB.resetForm(frm);
    $.each(data, function (key, value) {
      jsLIB.setValueField($(`[field=${key}]`, frm), value);
    });
    if (callback) callback();
  },

  populateOptions: function (objSelect, source, callback, options = {}) {
    var value = (objSelect.attr("opt-value") || "id");
    var label = (objSelect.attr("opt-label") || "ds");
    var search = (objSelect.attr("opt-search") || label);
    var subtext = (objSelect.attr("opt-subtext") || null);
    var selected = (objSelect.attr("opt-selected") || null);
    var disabled = (objSelect.attr("opt-disabled") || null);
    var group = (objSelect.attr("opt-group") || null);
    var dataContent = (objSelect.attr("opt-data-content") || null);
    var links = (objSelect.hasAttr("opt-links") ? objSelect.attr("opt-links").split(";") : null);

    var oLinkIcon = null;
    if (objSelect.hasAttr("opt-link-icons")) {
      oLinkIcon = [];
      objSelect.attr("opt-link-icons").split(";").forEach(function (linkIcon) {
        var lk = linkIcon.split('=');
        oLinkIcon[lk[0]] = lk[1];
      });
    }

    var oLinkClass = null;
    if (objSelect.hasAttr("opt-link-class")) {
      oLinkClass = [];
      objSelect.attr("opt-link-class").split(";").forEach(function (linkClass) {
        var lk = linkClass.split('=');
        oLinkClass[lk[0]] = lk[1];
      });
    }

    objSelect.children().remove();

    if ((objSelect.attr("auto-selected") == "true" || objSelect.attr("add-none") == "false") && source.length == 1) {
      source[0].sl = true;
      search = null;
      objSelect.attr('data-live-search', false);
    } else if ((!objSelect.hasClass("selectpicker") && !objSelect.hasClass("select2")) || !!objSelect.attr("add-none")) {
      if (objSelect.attr("add-none") === "true") objSelect.append($("<option></option>").attr("value", "").text("(NENHUM)"));
      else if (objSelect.attr("add-none") !== "false") objSelect.append($("<option></option>").attr("value", "").text(objSelect.attr("add-none")));
    }
    let lastGroup = null;
    let newOpt = null;
    let grp = null;

    if (objSelect.hasClass("selectpicker")) objSelect.selectpicker('refresh');

    $.each(source, function (idx, option) {
      newOpt = $("<option></option>")
        .attr("value", option[value])
        .text(option[label]);
      if (!!search && search != label) {
        let tokens = [option[search], option[label]];
        if (objSelect.hasClass("selectpicker")) newOpt.attr("data-tokens", (!!subtext ? [...tokens, option[subtext]] : tokens).join(' '));
        else if (objSelect.hasClass("select2")) newOpt.attr("data-custom-attribute", (!!subtext ? [...tokens, option[subtext]] : tokens).join(' '));
      }
      if (subtext) newOpt.attr("data-subtext", option[subtext]);
      if (selected && (option[selected] === true || option[selected] === 'S' || option[selected] === 1)) newOpt.attr("selected", "selected");
      if (disabled && (option[disabled] === true || option[disabled] === 'S' || option[disabled] === 1)) newOpt.attr("disabled", "");
      if (dataContent && !!option[dataContent]) newOpt.attr("data-content", option[dataContent]);
      if (!!links) links.forEach(function (link) {
        newOpt.attr(link, option[link]);
        if (!!oLinkClass) newOpt.attr('class', oLinkClass[option[link]]);
        if (!!oLinkIcon) newOpt.attr('data-icon', oLinkIcon[option[link]]);
      });
      if ((objSelect.hasClass("selectpicker") || objSelect.hasClass("select2")) && !!group && !!option[group]) {
        if (option[group] !== lastGroup) {
          if (lastGroup !== null) objSelect.append(grp);
          lastGroup = option[group];
          grp = $("<optgroup></optgroup>").attr("label", lastGroup)
        }
        grp.append(newOpt);
      } else {
        objSelect.append(newOpt);
      };
    });
    if (lastGroup !== null) objSelect.append(grp);

    if (objSelect.hasClass("selectpicker")) {
      objSelect.selectpicker('refresh');
      objSelect.selectpicker('render');
    } else if (objSelect.hasClass("select2")) {
      objSelect.select2({
        language: 'pt-BR',
        closeOnSelect: true,
        tags: true,
        ...options
      });
    }
    if (callback) callback(objSelect, source);
  },

  populateDomain: (objSelect, ajaxOptions, domain, callback) => jsLIB.ajax({
    ...ajaxOptions,
    success: function (data) {
      if (data.result !== true) return;
      jsLIB.populateOptions(objSelect, data[domain], callback);
    },
    error: true,
  }),

  downloadReport: (params, download = `Relatorio_${new Date().toISOString()}.pdf`) => jsLIB.ajax({
    url: params.url,
    data: params.data,
    async: false,
    success: function (res) {
      const downloadLink = document.createElement("a");
      downloadLink.href = 'data:application/pdf;base64,' + res;
      downloadLink.download = download;
      downloadLink.click();
      if (!!params.success) params.success();
    },
    error: function () {
      if (!!params.error) params.error();
    },
  }),

  pdfViewer: {
    progress: pct => {
      if (pct < 100) {
        if (pct == 0) {
          $('#pdfModal').modal('show');
          $('#pdfProgress').parent().show();
        };
        $('#pdfLoading').show();
        $('#pdfProgress')
          .css('width', `${Math.min(pct, 90)}% `)
          .attr('aria-valuenow', pct)
          .text(`${pct}% `);
      } else {
        $('#pdfProgress')
          .css('width', '100%')
          .attr('aria-valuenow', 100)
          .text('100%');
        $('#pdfProgress').parent().hide();
        $('#pdfLoading').hide();
      }
    },
    open: blob => {
      if (jsLIB.currentUrl) URL.revokeObjectURL(jsLIB.currentUrl);
      jsLIB.currentUrl = URL.createObjectURL(blob);
      $('#pdfFrame').attr('src', jsLIB.currentUrl);
      jsLIB.pdfViewer.progress(100);
    },
    close: () => {
      $('#pdfFrame').attr('src', '');
      if (jsLIB.currentUrl) {
        URL.revokeObjectURL(jsLIB.currentUrl);
        jsLIB.currentUrl = null;
      }
      $('#pdfModal').modal('hide');
    },
    load: url => {
      jsLIB.pdfViewer.progress(0);
      jsLIB.ajax({
        waiting: true,
        url,
        method: 'POST',
        xhrFields: { responseType: 'blob' },
        processData: false,
        contentType: false,
        dataType: false,
        onProgress: (p, evt) => {
          if (!!p) jsLIB.pdfViewer.progress(Math.floor(p * 100));
        },
        success: jsLIB.pdfViewer.open,
        error: true
      });
    },
  },

  navigateLogin: () => {
    if (window.myInterval) window.clearInterval(window.myInterval);
    window.location.replace(jsLIB.rootDir);
  },

  setStore: (key, val) => localStorage.setItem(`${jsLIB.prefix}-${key}`, val),

  getStore: key => localStorage.getItem(`${jsLIB.prefix}-${key}`),

  removeStore: key => localStorage.removeItem(`${jsLIB.prefix}-${key} `),

  toHHMMSSD: ms => {
    let decimals = (ms % 10);
    ms -= decimals;
    let hours = Math.floor(ms / 36000) % 36000;
    ms -= (hours * 36000);
    let minutes = Math.floor(ms / 600) % 600;
    ms -= (minutes * 600);
    let seconds = Math.floor(ms / 10);
    const result = [hours, minutes, seconds]
      .map((v, i, a) => (i > 0 && a[i - 1] > 0 && v < 10) ? `0${v} ` : v)
      .filter((v, i, a) => ((v !== "00" && v != 0) || !!a.find((aT, aI) => (aT > 0 && aI < i))))
      .join(':');
    return `${result}${result.length > 0 ? '.' : ''}${decimals} `;
  },

  populateLoginOptions: options => {
    jsLIB.dialogBox({
      title: 'Seleção Cadastral',
      message: `<div class="row">
      <div class="col-12 form-group">
        <label for="cmbOptions"><b>Selecione o cadastro que deseja logar:</b></label><select class="selectpicker" id="cmbOptions" opt-subtext="cn" opt-search="sh" opt-data-content="dc" data-live-search="true" data-title="Selecione a opção desejada" data-width="100%" data-container="body" data-actions-box="false" data-size="10"></select>
      </div></div>`,
      type: BootstrapDialog.TYPE_INFO,
      closeByBackdrop: true,
      closeByKeyboard: true,
      onshown: function (dialogRef) {
        jsLIB.populateOptions($("#cmbOptions"), options);
        dialogRef.getModalFooter().css('display', 'block');
        dialogRef.getModalHeader().find('.bootstrap-dialog-title')
          .css("color", '#FFFFFF');
        dialogRef.getModalDialog().addClass('modal-dialog-centered');
        $('.login-box').visible(false);
      },
      onhidden: function () {
        $('.login-box').visible(true);
      },
      buttons: [
        {
          icon: 'fas fa-xmark',
          label: 'Fechar',
          cssClass: 'btn-secondary float-left',
          action: function (dialogRef) {
            dialogRef.close();
          }
        },
        {
          label: 'Confirmar seleção Login',
          icon: 'fas fa-right-to-bracket',
          cssClass: 'btn-primary float-right',
          autospin: true,
          action: function (dialogRef) {
            const [sgc, email, birthDate] = $("#cmbOptions").val().split('|');
            jsLIB.ajax({
              waiting: true,
              url: `${jsLIB.rootDir}app/api/login/`,
              data: {
                MethodName: 'login',
                data: window.btoa(JSON.stringify({ sgc, email, birthDate }))
              },
              success: function (data) {
                if (data.login == true) {
                  window.location.replace(data.page);
                } else {
                  jsLIB.notification({
                    title: 'Acesso negado!',
                    subtitle: 'Dados inválidos',
                    body: 'Verifique os dados e tente novamente mais tarde.'
                  });
                }
              },
              error: true,
            });
          }
        }
      ]
    });
  },
};

var SPMaskBehavior = function (val) {
  return val.replace(/\D/g, '').length === 11 ? '(00) 00000-0000' : '(00) 0000-00009';
};

var spOptions = {
  onKeyPress: function (val, e, field, options) {
    field.mask(SPMaskBehavior.apply({}, arguments), options);
  }
};

$.fn.selectpicker.defaults = {
  noneSelectedText: '(NENHUM)',
  noneResultsText: 'Nada encontrado contendo {0}',
  countSelectedText: 'Selecionado {0} de {1}',
  maxOptionsText: ['Limite excedido (máx. {n} {var})', 'Limite do grupo excedido (máx. {n} {var})', ['itens', 'item']],
  multipleSeparator: ', ',
  deselectAllText: 'Desmarcar Todos',
  selectAllText: 'Marcar Todos'
};

var jsFilter = {
  filtered: false,

  apply: () => {
    jsFilter.filtered = true;
    var obj = $($("#divFilters").attr("filter-to"));
    if (obj) {
      if (obj.is("SELECT")) {
        obj.trigger("reload.options.bs.select");
      } else if (obj.is("TABLE")) {
        obj.DataTable().ajax.reload();
      }
    }
  },

  jSON: () => {
    var retorno = {};
    $("#divFilters select").each(function (i, obj) {
      var reg = {
        vl: $(obj).val(),
        fg: $("#notFilter" + $(obj).attr("filter-field")).prop('checked')
      };
      retorno[$(obj).attr("filter-field")] = reg;
    });
    return retorno;
  },

  removeAll: () => {
    $("[filter-value]").each(function () {
      jsFilter.removeFilter(this);
    });
  },

  removeFilter: objFilter => {
    var obj = $(objFilter);
    var value = obj.attr("filter-value");
    var label = obj.attr("filter-label");
    var unique = obj.attr("filter-unique");

    var option = $("<option></option>")
      .attr("value", value)
      .text(label);
    if (unique == 'true') {
      option.attr("data-tokens", "unique");
    }

    $("#addFilter").append(option);
    $("#addFilter").html($("#addFilter").children('option').sort(function (x, y) {
      return $(x).text().toUpperCase() < $(y).text() ? - 1 : 1;
    }));
    $("#addFilter").val("").selectpicker('refresh');
    $("#optFilter" + value).selectpicker('destroy');
    $("#divFilter" + value).remove();
    if (jsFilter.filtered) {
      jsFilter.apply();
    }
    if ($("#divFilters select").length == 0) {
      $("#applyFilter").hide();
      jsFilter.filtered = false;
    }
  },

  addFilter: objFilter => {
    var obj = $(objFilter);
    var label = obj.find('option:selected').text();
    var unique = obj.find('option:selected').attr('data-tokens') == 'unique';
    var value = obj.val();

    if (value != "") {
      var flt = jsLIB.ajax({ url: `${jsLIB.rootDir}app/api/add-filter/`, data: { MethodName: 'getFilter', data: { type: value } } });
      if (flt.result) {
        var strAppend =
          "<div class=\"input-group input-group-sm col-xs-12 col-md-12 col-sm-12 col-lg-12\" id=\"divFilter" + value + "\" style=\"padding-bottom:10px\">" +
          "<label for=\"optFilter" + value + "\" class=\"pull-left\">" + label + ":&nbsp;</label>" +
          "<span class=\"label label-danger pull-right\" style=\"cursor:pointer\" onclick=\"jsFilter.removeFilter(this);\" filter-unique=\"" + unique + "\" filter-value=\"" + value + "\" filter-label=\"" + label + "\"><i class=\"glyphicon glyphicon-remove\"></i>&nbsp;Remover</span>";
        if (flt.domain.length > 5 && !unique) {
          strAppend += "<span class=\"pull-right\"><label for=\"notFilter" + value + "\"><input type=\"checkbox\" id=\"notFilter" + value + "\"><span class=\"text\">N&atilde;o</span></label>&nbsp;&nbsp;</span>";
        }
        strAppend += "<select class=\"selectpicker form-control input-sm\" id=\"optFilter" + value + "\" filter-field=\"" + value + "\"" + (!unique ? " multiple data-selected-text-format=\"count > 3\"" : "") + " title=\"Escolha uma ou mais op&ccedil;&otilde;es\" data-width=\"100%\" data-container=\"body\"";
        if (flt.domain.length > 8 && !unique) {
          strAppend += " data-live-search=\"true\"";
          strAppend += " data-actions-box=\"true\"";
        }
        strAppend += " opt-subtext=\"sub\"></select>" +
          "</div>";

        $("#divFilters").append(strAppend);
        jsLIB.populateOptions($("#optFilter" + value), flt.domain);
        $("#optFilter" + value).selectpicker();
        $("#addFilter option[value='" + value + "']").remove();
        $("#addFilter").val("").selectpicker('refresh');
        $("#applyFilter").show();
      }
    }
  }

};

var myApp = angular.module('angular-app', []);
