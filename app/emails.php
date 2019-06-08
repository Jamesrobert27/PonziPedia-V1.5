<?php
$EmaiMessage = ' <table style="font-family:"Helvetica Neue","Helvetica",Helvetica,Arial,sans-serif;font-size:100%;line-height:1.6;width:100%;margin:0;padding:20px;background-color: #f6f6f6;">
	<tbody>
		<tr>
			<td style="padding:0"></td>
			<td bgcolor="#FFFFFF" style="display:block!important;max-width:600px!important;clear:both!important;margin:0 auto;padding:20px;border:1px solid #f0f0f0">
				<div style="max-width:600px;display:block;margin:0 auto">
					<table>
						<tbody>
							<tr>
								<td style="margin:0px;padding:0px;font-size: 14px;line-height:1.6;font-weight:normal;padding:0px">
									<h1 style="font-size :30px; color: #dc3545;">'.Config::get('app.name').'<br></h1>
                                   '.$message.'
                                   <br>
                                   <div style="background-color: #4ecdc4;border-color: #4c5764;border: 2px solid #45b7af;padding: 10px;text-align: center;">
            <a style="display: block;color: #ffffff;font-size: 12px;text-decoration: none;text-transform: uppercase;" href="'.App::url('account/').'">
                My Account
            </a>
        </div>
									<p style="font-size:11px"><br>Please do not reply to this message; it was sent from an unmonitored email address.</p>
								</td>
							</tr>
						</tbody>
					</table>
				</div>		
			</td>
			<td style="padding:0"></td>
		</tr>
	</tbody>
</table>';
?>