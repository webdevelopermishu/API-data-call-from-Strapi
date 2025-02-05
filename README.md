# API-data-call-from-Strapi
<h1>REST API Job Fetcher – WordPress Plugin</h1>
<h2>Description</h2>
<strong>REST API Job Fetcher</strong> is a WordPress plugin that fetches job listings from multiple external APIs and automatically inserts them into custom post types. It supports different job sources with unique field mappings and ensures seamless integration with the WordPress REST API for easy access and display.
<h2>Features</h2>
<ul>
 	<li>Fetches job listings from multiple APIs</li>
 	<li>Creates custom post types for job details</li>
 	<li>Automatically prevents duplicate job entries</li>
 	<li>Stores job metadata using WordPress custom fields</li>
 	<li>Exposes job details via the REST API</li>
 	<li>Fully customizable and extendable</li>
</ul>
<h2>APIs Supported</h2>
<ol>
 	<li><strong>Featured Jobs API</strong> – <code>https://colorful-cherry-497ddb593b.strapiapp.com/api/featured-jobs</code></li>
 	<li><strong>Google Jobs API</strong> – <code>https://colorful-cherry-497ddb593b.strapiapp.com/api/google-jobs</code></li>
</ol>
<h2>Installation</h2>
<ol>
 	<li>Upload the plugin to your WordPress <code>/wp-content/plugins/</code> directory.</li>
 	<li>Activate the plugin from the WordPress admin panel.</li>
 	<li>Jobs will be automatically fetched and stored in their respective post types.</li>
</ol>
<h2>Usage</h2>
<ul>
 	<li>Navigate to <strong>Job Details</strong> in the WordPress admin panel to view or edit job posts.</li>
 	<li>Use the REST API endpoint <code>/wp-json/wp/v2/job-details</code> to retrieve job data programmatically.</li>
</ul>
<h2>Future Enhancements</h2>
<ul>
 	<li>Admin settings to manage API sources dynamically</li>
 	<li>Improved UI for job management</li>
 	<li>Cron job support for scheduled fetching</li>
</ul>
<h2>Contribute</h2>
Feel free to submit pull requests or open issues for feature requests and bug reports! 🚀
