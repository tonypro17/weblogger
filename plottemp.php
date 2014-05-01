<?php 
	header ("Content-type: image/png"); 
	
	$db = new SQLite3('/home/pi/Desktop/331proj2/temperatures.db') or die ('Unable to open database');

	$data = $db->query('SELECT * FROM temptable DESC LIMIT 1440');

	$max_x = 720;
	$max_y = 350;

	$x_offset = 75;
	$y_offset = 50;

	$bottombar = 50;
	$rightbar = 50;

	$graph = ImageCreate($rightbar+$max_x+$x_offset,$max_y+$y_offset+$bottombar) or die ("Failure");
	
//	ImageFilledRectangle($graph, 0,0,$max_x+$x_offset,$y_offset,$white);
//	ImageFilledRectangle($graph, 0,0,$x_offset,$max_y+$y_offset,$white);
	$bg_color=ImageColorAllocate($graph,0,25,0);
	$line_color=ImageColorAllocate($graph,0,200,0);
	$bg_line_color=ImageColorAllocate($graph,0,35,0);
	$white = ImageColorAllocate($graph,0,35,0);
	$txt_color=ImageColorAllocate($graph,0,200,0);

	// Tick marks on the x-axis once every 30 min
	// Also create vertical gridlines
	for($i=$x_offset;$i<($max_x+$x_offset);$i=$i+($max_x/24))
	{
		ImageLine ($graph, $i, $max_y+$y_offset, $i, $y_offset, $bg_line_color);
		ImageLine ($graph, $i, $max_y+$y_offset, $i, (($max_y+$y_offset)-5), $line_color);
	}

	// Tick marks on y-axis, once every 10 degrees
	// Also create horizontal gridlines
	for($j=0;$j<=$max_y;$j=$j+30)
	{
		ImageLine ($graph, $x_offset,($max_y+$y_offset)-$j, $max_x+$x_offset, ($max_y+$y_offset)-$j, $bg_line_color);
		ImageLine ($graph, $x_offset,(($max_y+$y_offset)-$j),$x_offset+5,(($max_y+$y_offset)-$j), $line_color);
	}

	$row=$data->fetchArray();
	$lastx=$x_offset;
	$lasty=(($max_y+$y_offset)-(3*$row[2]));

	while($row) {
		$thisx = $lastx+1;
		$thisy = (($max_y+$y_offset)-($row[2]*3));
		ImageLine ($graph, $lastx, $lasty, $thisx, $thisy, $line_color);
		$row=$data->fetchArray();
		$lastx = $thisx;
		$lasty = $thisy;
	}
	
	ImageFilledRectangle($graph, 0,0,$max_x+$x_offset,$y_offset,$white);
        ImageFilledRectangle($graph, 0,0,$x_offset,$max_y+$y_offset,$white);
	ImageFilledRectangle($graph, 0,$max_y+$y_offset,$max_x+$x_offset,$max_x+$x_offset+$bottombar,$white);
	ImageFilledRectangle($graph, $max_x+$x_offset,0,$max_x+$x_offset+$rightbar,$max_y+$y_offset+$bottombar,$white);

	ImageString ($graph, 5,300,18, "Raspberry Pi Temperature Logger",$txt_color);
	ImageStringUp ($graph, 5,5,375, "Temperature (Degrees Fahrenheit)",$txt_color);
	ImageString ($graph, 5,350,425, "Time (Hours Ago)",$txt_color);
	
	$hour = 24;
        for($i=$x_offset;$i<=($max_x+$x_offset);$i=$i+($max_x/24))
        {
                ImageString ($graph, 5, ($i-5), 405, $hour, $txt_color);
		$hour = $hour - 1;
	}

	$tempinc=0;
	for($j=0;$j<=$max_y;$j=$j+30)
        {
		ImageString($graph, 5, ($x_offset-30),(($max_y+$y_offset)-$j-10),$tempinc, $txt_color);
		$tempinc = $tempinc + 10;
	}

	ImagePng ($graph);	
?>
