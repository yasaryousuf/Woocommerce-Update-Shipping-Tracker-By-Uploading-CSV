function progressHandler(event) {
  document.getElementById("wcust-loaded_n_total").innerHTML =
    "Uploaded " + event.loaded + " bytes of " + event.total;
  var percent = (event.loaded / event.total) * 100;
  document.getElementById("wcust-progressBar").value = Math.round(percent);
  document.getElementById("wcust-status").innerHTML =
    Math.round(percent) + "% uploaded... please wait";
}

function completeHandler(event) {
  var res = JSON.parse(event.target.responseText);
  console.log();
  document.getElementById("wcust-status").innerHTML = res.data.message;
  document.getElementById("wcust-progressBar").value = 0;
}

function errorHandler(event) {
  document.getElementById("wcust-status").innerHTML = "Upload Failed";
}

function abortHandler(event) {
  document.getElementById("wcust-status").innerHTML = "Upload Aborted";
}

jQuery(function ($) {
  $(".wcust-btn.reload").click(function (e) {
    location.reload();
  });

  $("[name='wcust-file']").change(function (e) {
    var file = $("[name='wcust-file']")[0].files[0];
    // alert(file.name+" | "+file.size+" | "+file.type);
    var formdata = new FormData();
    formdata.append("file", file);
    var ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.addEventListener("error", errorHandler, false);
    ajax.addEventListener("abort", abortHandler, false);
    ajax.open(
      "POST",
      ajaxurl + "?action=upload_woocommerce_shipping_tracking_csv"
    );
    ajax.send(formdata);

    // $.ajax({
    //   type: "POST",
    //   url: frontend_form_object.ajaxurl + "?action=save_registration_form_type",
    //   data: formdata,
    //   success: function (data) {
    //     handleData(data);
    //   },
    //   progress: function (data) {
    //     handleData(data);
    //   },
    //   progressUpload: function (data) {
    //     handleData(data);
    //   },
    // }).done(function (response) {
    //   location.reload();
    // });
  });
});
