<link href="./css/matrix.css" rel="stylesheet">
<script src="./scripts/matrix.js"></script>

<div class="container-fluid">
	<div class="row-fluid">

		<!-- Sidebar content -->
		<div class="col-xs-2 c1">
			<form
				name="filters-form"
				role="form"
				action=""
				>
				<ul class="list-unstyled">
					<li>
						Pay Level
						<span class="glyphicon glyphicon-triangle-bottom"></span>
						<ul>
							<li>
								<input type="checkbox" id="">
								All
								<span class="glyphicon glyphicon-triangle-bottom"></span>
								<ul>
									<?php
										for ($i=10; $i<=19; $i++) {
									?>
									<li>
										<input type="checkbox" id="">
										<?php echo $i; ?>
									</li>
									<?php
										}
									?>
								</ul>
							</li>
						</ul>
					</li>
				</ul>
			</form>
		</div>

		<!-- Body Content -->
		<div class="col-xs-10 c2">
			Test 2
		</div>
	</div>
	

</div>