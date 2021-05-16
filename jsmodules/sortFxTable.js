function sortFxTable() {
  var table, i, x, y;
  table = document.getElementById("fxtable");
  var switching = true;
   
  var rows = table.rows;
  var NoDR = rows.length - 1;
   
  // Run loop through all rows (place element)
  for (i = 2; i <= NoDR; i++) {
    switching = false;
    x = rows[i].getElementsByTagName("TD")[1].getElementsByTagName("A")[0];
   
    // Run loop through all rows (check element)
    for (j = 1; j < NoDR; j++) {
      // Fetch element that needs to be compared
      y = rows[j].getElementsByTagName("TD")[1].getElementsByTagName("A")[0];
    
      // Check if row needs to be inserted
      if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
        // If yes, break
        switching = true;
        break;
      }
    }
    if (switching) {
      rows[i].parentNode.insertBefore(rows[i], rows[j]);
    }
    else {}
  }
}

sortFxTable();