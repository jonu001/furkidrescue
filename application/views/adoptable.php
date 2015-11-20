<div class="container">
  
  <div class="text-center">
	<?php 
		foreach ($pets->result() as $row)
		{
		echo $row->name;
		}
	?>
   </div>
  
</div>