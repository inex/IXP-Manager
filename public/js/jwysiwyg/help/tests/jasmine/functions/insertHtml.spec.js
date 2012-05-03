describe("Wysiwyg.insertHtml", function () {
	beforeEach(function () {
		$("#id1").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible: true },
				html: { visible: true }
			}
		});
	});

	afterEach(function () {
		$("textarea").wysiwyg("clear").wysiwyg("destroy");
	});

	it('should insert correct html text 1', function () {
		$("#id1").wysiwyg("insertHtml", "<p>111</p>");
		var content = $("#id1").val();

		expect(content.indexOf("<p>111</p>")).not.toEqual(-1);
	});

	it('should insert correct plain text', function () {
		$("#id1").wysiwyg("insertHtml", "plain text");
		var content = $("#id1").val();

		expect(content.indexOf("plain text")).not.toEqual(-1);
	});

	it('should insert correct html text 2', function () {
		$("#id1").wysiwyg("insertHtml", "<p>111</p><p>222</p><p>333</p>");
		var content = $("#id1").val();

		expect(content.indexOf("<p>111</p><p>222</p><p>333</p>")).not.toEqual(-1);
	});
	
	it('should insert correct html text 3', function () {
		$("#id1").wysiwyg("insertHtml", "text<p>111</p><p>222</p><p>333</p>text");
		var content = $("#id1").val();

		expect(content.indexOf("text<p>111</p><p>222</p><p>333</p>text")).not.toEqual(-1);
	});
});
