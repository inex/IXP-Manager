            <h2>NOC Details</h2>

			<table border="0" style="margin-left: 30px;">
				<tr>
					<td><strong>Phone</strong></td>
					<td width="20"></td>
					<td>{$customer.nocphone}</td>
				</tr>
				<tr>
					<td><strong>24 Hour Phone</strong></td>
					<td></td>
					<td>{$customer.noc24hphone}</td>
				</tr>
				<tr>
					<td><strong>Fax</strong></td>
					<td></td>
					<td>{$customer.nocfax}</td>
				</tr>
				<tr>
					<td><strong>Email</strong></td>
					<td></td>
					<td>{$customer.nocemail}</td>
				</tr>
				<tr>
					<td><strong>Hours</strong></td>
					<td></td>
					<td>{$customer.nochours}</td>
				</tr>
				<tr>
					<td><strong>WWW</strong></td>
					<td></td>
					<td>{$customer.nocwww}</td>
				</tr>
			</table>


            <h2>Billing Details</h2>

			<table border="0" style="margin-left: 30px;">
				<tr>
					<td><strong>Contact Person</strong></td>
					<td width="20"</td>
					<td>{$customer.billingContact}</td>
				</tr>
				<tr>
					<td valign="top"><strong>Address</strong></td>
					<td width="20"</td>
					<td>
						{$customer.billingAddress1}<br />
						{$customer.billingAddress2}<br />
						{$customer.billingCity}<br />
						{$customer.billingCountry}
					</td>
				</tr>
			</table>
