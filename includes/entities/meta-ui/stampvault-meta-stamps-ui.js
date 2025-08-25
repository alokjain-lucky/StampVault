// JavaScript for dynamic catalog codes meta box in StampVault
(function($){
    $(document).on("click", "#stampvault-add-catalog-row", function(e){
        e.preventDefault();
        var $table = $("#stampvault-catalog-codes-table tbody");
        var idx = $table.find("tr").length;
        var catalogs = window.stampvaultCatalogList || [];
        var row = "<tr>";
        row += "<td style='padding:6px 4px;'><select name=\"catalog_codes["+idx+"][catalog]\" style='width:100%;'>";
        for(var i=0;i<catalogs.length;i++){row+='<option value="'+catalogs[i]+'">'+catalogs[i]+'</option>';}
        row += "</select></td>";
        row += "<td style='padding:6px 4px;'><input type=\"text\" name=\"catalog_codes["+idx+"][code]\" class=\"widefat\" style='width:100%;' /></td>";
        row += "<td style='padding:6px 4px; text-align:center;'><button type=\"button\" class=\"button stampvault-remove-catalog-row\" style='background:#e74c3c; color:#fff; border:none;'>Remove</button></td>";
        row += "</tr>";
        $table.append(row);
    });
    $(document).on("click", ".stampvault-remove-catalog-row", function(){
        $(this).closest("tr").remove();
    });
})(jQuery);
