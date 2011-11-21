
<table border="0">
<tr>
	<td>
        <form action="{genUrl controller=$controller action='add'}" method="post">
            <input type="submit" name="submit" class="button" value="Add New" />
        </form>
    </td>
    <td width="30"></td>
    <td>
        <form action="{genUrl controller=vlan action='quick-add'}" method="post">
            <input type="submit" name="submit" class="button" value="Quick Add" />
        </form>
    </td>
</tr>
</table>
