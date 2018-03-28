<?php include("application/views/admin/common/header.php");?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

    <head>

    <!-- Basic Page Needs
  ================================================== -->
    <meta charset="utf-8">
    <title>Reports - Astor</title>
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Mobile Specific Metas
  ================================================== -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- CSS
  ================================================== -->
    <link rel="stylesheet" href="stylesheets/demo_table.css">
    <link rel="stylesheet" href="stylesheets/base.css">
    <link rel="stylesheet" href="stylesheets/astor/jquery-ui-1.8.17.custom.css" />
    <link rel="stylesheet" href="stylesheets/skeleton.css">
    <link rel="stylesheet" href="stylesheets/layout.css">

    <!-- Favicons
	================================================== -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/apple-touch-icon-114x114.png">

    <!-- Head Scripts
	================================================== -->
    <script src="js/modernizr-2.5.1.js"></script>
    <script src="js/jquery-1.7.1.js"></script>
    <script src="js/jquery-ui-1.8.17.custom.min.js"></script>
    <script src="js/jquery.dataTables.js"></script>
    <script src='js/jquery.color-RGBa-patch.js'></script>
    <script src="js/custom.js"></script>
    <script>
	$(function() {
		$('#tabs').tabs();
				$('#wastelist').dataTable( {
					"sPaginationType": "full_numbers",
					"bPaginate": false,
					"bFilter": false
				} );
		$('#costlist').dataTable( {
					"sPaginationType": "full_numbers",
					"bPaginate": false,
					"bFilter": false
				} );
	$( "#radio" ).buttonset();
	});
	</script>
    </head>
    <body style="background:#ffffff;">

<!-- Primary Page Layout
	================================================== -->

<article class="container" >
      <div class="content" style="border:0px;">
        <div class="row"><br><h4><strong>Distribution Centers by Month</strong></h4><h5>From 01/2012 to 02/2012</h5><br><img src="http://chart.apis.google.com/chart?chxl=0:|02%2F2012|01%2F2012|1:|25|50|75|100&chxp=1,25,50,75,100&chxr=1,0,125&chxs=0,000000,14,0.5,l,676767&chxt=x,y&chbh=r&chs=500x300&cht=bvs&chco=80C65A,FF9900&chd=s:bS,u0&chdl=Recycling+(Tons)|Waste+(Tons)&chdlp=b&chg=20,25&chtt=Waste%2FRecycling+from+01%2F2012+-+02%2F2012" width="500" height="300" alt="Waste/Recycling from 01/2012 - 02/2012" /><img src="http://chart.apis.google.com/chart?chs=550x275&cht=p&chd=e:infdmzmzp9T1a3VHPWWZj1q3FHFwGZgoOEVHijf.dbXr&chdlp=b&chl=Anderson++5%25+(%2454)|Berkeley+5%25+(%2449k)|Bolingbrook+5%25+(%246k)|Conneticutt+5%25|Flagstaff+5%25|Houston+5%25|Jupiter+5%25|Kutztown+5%25|Lehigh+5%25|Moreno+Valley+5%25|Mt+Vernon+5%25|Nazareth+5%25|Orlando+5%25|Pendergast+5%25|Portage+5%25|Pureto+Rico+5%25|Rogers+5%25|Tempe+5%25|Waxahachie+5%25+(%2454k)|Windsor+5%25|Woodland+5%25|Perrysberg+2%25&chtt=Waste+from+01%2F2012+-+02%2F2012&chts=000000,11.5" width="550" height="275" alt="Waste from 01/2012 - 02/2012" /><br><Br><table cellpadding="0" cellspacing="0" border="0" class="display" id="wastelist" width="100%">
	<thead>
		<tr>
		  <th>AU</th>
		  <th>Name</th>
		  <th>SqFt</th>
		  <th>Period</th>
		  <th>Waste</th>
			<th>Cardboard</th>
            <th>Alumnim</th>
			<th>Plastic</th>
			<th>Total Tonnage</th>
			</tr>
	</thead>
	<tbody>
  <tr class="odd gradeA">
    <td>610018</td>
    <td><a href="dcexample.html">Anderson </a></td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td>610002</td>
    <td>Berkley </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>439210</td>
    <td>Bolingbrook</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td class="even gradeB">610017</td>
    <td class="even gradeB">Connecticut </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>610009</td>
    <td>Flagstaff </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td>610041</td>
    <td>Houston</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td class="odd gradeX">610001</td>
    <td class="odd gradeX">Jupiter</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td>610040</td>
    <td>Kutztown</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>610007</td>
    <td>Lehigh</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td>610012</td>
    <td>Moreno Valley </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>610008</td>
    <td>Mt Vernon </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td></td>
    <td>Nazareth </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610004</td>
    <td>Orlando </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610042</td>
    <td>Pendergrass</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610011</td>
    <td>Perrysburg</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610016</td>
    <td>Portage</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610024</td>
    <td>Puerto Rico </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610043</td>
    <td>Rogers</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>821710</td>
    <td>Tempe </td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610010</td>
    <td>Waxahachie</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610003</td>
    <td>Windsor</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610006</td>
    <td>Woodlands</td>
    <td>10,000</td>
    <td>01/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>610018</td>
    <td><a href="dcexample.html">Anderson </a></td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>121</td>
    </tr>
  <tr class="even gradeA">
    <td>610002</td>
    <td>Berkley </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>439210</td>
    <td>Bolingbrook</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>100</td>
    <td>6</td>
    <td>6</td>
    <td>6</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td class="even gradeB">610017</td>
    <td class="even gradeB">Connecticut </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td class="odd gradeA">100</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td class="odd gradeA">6</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>610009</td>
    <td>Flagstaff </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
        <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td>610041</td>
    <td>Houston</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
        <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td class="odd gradeX">610001</td>
    <td class="odd gradeX">Jupiter</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
        <td>6</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td>610040</td>
    <td>Kutztown</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
        <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>610007</td>
    <td>Lehigh</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr class="even gradeA">
    <td>610012</td>
    <td>Moreno Valley </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr class="odd gradeA">
    <td>610008</td>
    <td>Mt Vernon </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td></td>
    <td>Nazareth </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610004</td>
    <td>Orlando </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610042</td>
    <td>Pendergrass</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610011</td>
    <td>Perrysburg</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610016</td>
    <td>Portage</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610024</td>
    <td>Puerto Rico </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>121</td>
    </tr>
  <tr  class="even gradeA">
    <td>610043</td>
    <td>Rogers</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>821710</td>
    <td>Tempe </td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610010</td>
    <td>Waxahachie</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="odd gradeA">
    <td>610003</td>
    <td>Windsor</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  <tr  class="even gradeA">
    <td>610006</td>
    <td>Woodlands</td>
    <td>10,000</td>
    <td>02/2012</td>
    <td>6</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>12</td>
    </tr>
  </tbody>
	<tfoot>
		<tr>
		  <th>AU</th>
		  <th>Name</th>
		  <th>SqFt</th>
		  <th>Period</th>
		  <th>Waste</th>
		  <th>Cardboard</th>
		  <th>Aluminum</th>
		  <th>Plastic</th>
		  <th>Total Tonnage</th>
		  </tr>
	</tfoot>
</table><br></div>

		</div>
 </div>
<?php include("application/views/admin/common/footer.php");?>