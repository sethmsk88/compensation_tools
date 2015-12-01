<style>
#canvasTable{
	margin-top:20px;
	border: 1px solid black;
}

td{
	width:300px;
	height:100px;
}


.outsideWrapper{
	width:300px;
	height:100px;
}
.insideWrapper{
	width:100%;
	height:100%;
	position:relative;
}
.canvasTable{
	width:100%;
	height:100%;
	position:absolute;
	top:0px;
	left:0px;
}
.coveringCanvas{
	width:100%;
	height:100%;
	position:absolute;
	top:0px;
	left:0px;
	
}
</style>

<div class="container">
	<div class="row">
		<div class="outsideWrapper">
			<div class="insideWrapper">
				<table id="canvasTable">
					<tr>
						<td>&nbsp;</td>
					</tr>
				</table>
				<canvas id="myCanvas" class="coveringCanvas"></canvas>
				<script>
					var context = document.getElementById('myCanvas').getContext('2d');
					context.beginPath();
					context.moveTo(150, 0);
					context.lineTo(150, 150);
					context.stroke();
				</script>
			</div>
		</div>
	</div>
</div>