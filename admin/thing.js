function singleSelectChangeValue() {
    var selObj = document.getElementById("singleSelect");
    var selValue = selObj.options[selObj.selectedIndex].value;
    document.getElementById("category").value = selValue;
}