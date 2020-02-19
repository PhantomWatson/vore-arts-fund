<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

	override(
		\Cake\Controller\Controller::loadComponent(0),
		map([
			'Auth' => \Cake\Controller\Component\AuthComponent::class,
			'Cookie' => \Cake\Controller\Component\CookieComponent::class,
			'Csrf' => \Cake\Controller\Component\CsrfComponent::class,
			'DebugKit.Toolbar' => \DebugKit\Controller\Component\ToolbarComponent::class,
			'Flash' => \Cake\Controller\Component\FlashComponent::class,
			'Paginator' => \Cake\Controller\Component\PaginatorComponent::class,
			'RequestHandler' => \Cake\Controller\Component\RequestHandlerComponent::class,
			'Security' => \Cake\Controller\Component\SecurityComponent::class,
		])
	);

	override(
		\Cake\Core\PluginApplicationInterface::addPlugin(0),
		map([
			'Bake' => \Cake\Http\BaseApplication::class,
			'DebugKit' => \Cake\Http\BaseApplication::class,
			'IdeHelper' => \Cake\Http\BaseApplication::class,
			'Migrations' => \Cake\Http\BaseApplication::class,
			'WyriHaximus/TwigView' => \Cake\Http\BaseApplication::class,
		])
	);

	override(
		\Cake\Database\Type::build(0),
		map([
			'biginteger' => \Cake\Database\Type\IntegerType::class,
			'binary' => \Cake\Database\Type\BinaryType::class,
			'binaryuuid' => \Cake\Database\Type\BinaryUuidType::class,
			'boolean' => \Cake\Database\Type\BoolType::class,
			'date' => \Cake\Database\Type\DateType::class,
			'datetime' => \Cake\Database\Type\DateTimeType::class,
			'decimal' => \Cake\Database\Type\DecimalType::class,
			'float' => \Cake\Database\Type\FloatType::class,
			'integer' => \Cake\Database\Type\IntegerType::class,
			'json' => \Cake\Database\Type\JsonType::class,
			'smallinteger' => \Cake\Database\Type\IntegerType::class,
			'string' => \Cake\Database\Type\StringType::class,
			'text' => \Cake\Database\Type\StringType::class,
			'time' => \Cake\Database\Type\TimeType::class,
			'timestamp' => \Cake\Database\Type\DateTimeType::class,
			'tinyinteger' => \Cake\Database\Type\IntegerType::class,
			'uuid' => \Cake\Database\Type\UuidType::class,
		])
	);

	override(
		\Cake\Datasource\ModelAwareTrait::loadModel(0),
		map([
			'Applications' => \App\Model\Table\ApplicationsTable::class,
			'Categories' => \App\Model\Table\CategoriesTable::class,
			'DebugKit.Panels' => \DebugKit\Model\Table\PanelsTable::class,
			'DebugKit.Requests' => \DebugKit\Model\Table\RequestsTable::class,
			'FundingCycles' => \App\Model\Table\FundingCyclesTable::class,
			'Images' => \App\Model\Table\ImagesTable::class,
			'Messages' => \App\Model\Table\MessagesTable::class,
			'Notes' => \App\Model\Table\NotesTable::class,
			'Statuses' => \App\Model\Table\StatusesTable::class,
			'Users' => \App\Model\Table\UsersTable::class,
			'Votes' => \App\Model\Table\VotesTable::class,
		])
	);

	override(
		\Cake\Datasource\QueryInterface::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
		])
	);

	override(
		\Cake\ORM\Association::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
		])
	);

	override(
		\Cake\ORM\Locator\LocatorInterface::get(0),
		map([
			'Applications' => \App\Model\Table\ApplicationsTable::class,
			'Categories' => \App\Model\Table\CategoriesTable::class,
			'DebugKit.Panels' => \DebugKit\Model\Table\PanelsTable::class,
			'DebugKit.Requests' => \DebugKit\Model\Table\RequestsTable::class,
			'FundingCycles' => \App\Model\Table\FundingCyclesTable::class,
			'Images' => \App\Model\Table\ImagesTable::class,
			'Messages' => \App\Model\Table\MessagesTable::class,
			'Notes' => \App\Model\Table\NotesTable::class,
			'Statuses' => \App\Model\Table\StatusesTable::class,
			'Users' => \App\Model\Table\UsersTable::class,
			'Votes' => \App\Model\Table\VotesTable::class,
		])
	);

	override(
		\Cake\ORM\Table::addBehavior(0),
		map([
			'CounterCache' => \Cake\ORM\Table::class,
			'DebugKit.Timed' => \Cake\ORM\Table::class,
			'Timestamp' => \Cake\ORM\Table::class,
			'Translate' => \Cake\ORM\Table::class,
			'Tree' => \Cake\ORM\Table::class,
		])
	);

	override(
		\Cake\ORM\Table::belongToMany(0),
		map([
			'Applications' => \Cake\ORM\Association\BelongsToMany::class,
			'Categories' => \Cake\ORM\Association\BelongsToMany::class,
			'DebugKit.Panels' => \Cake\ORM\Association\BelongsToMany::class,
			'DebugKit.Requests' => \Cake\ORM\Association\BelongsToMany::class,
			'FundingCycles' => \Cake\ORM\Association\BelongsToMany::class,
			'Images' => \Cake\ORM\Association\BelongsToMany::class,
			'Messages' => \Cake\ORM\Association\BelongsToMany::class,
			'Notes' => \Cake\ORM\Association\BelongsToMany::class,
			'Statuses' => \Cake\ORM\Association\BelongsToMany::class,
			'Users' => \Cake\ORM\Association\BelongsToMany::class,
			'Votes' => \Cake\ORM\Association\BelongsToMany::class,
		])
	);

	override(
		\Cake\ORM\Table::belongsTo(0),
		map([
			'Applications' => \Cake\ORM\Association\BelongsTo::class,
			'Categories' => \Cake\ORM\Association\BelongsTo::class,
			'DebugKit.Panels' => \Cake\ORM\Association\BelongsTo::class,
			'DebugKit.Requests' => \Cake\ORM\Association\BelongsTo::class,
			'FundingCycles' => \Cake\ORM\Association\BelongsTo::class,
			'Images' => \Cake\ORM\Association\BelongsTo::class,
			'Messages' => \Cake\ORM\Association\BelongsTo::class,
			'Notes' => \Cake\ORM\Association\BelongsTo::class,
			'Statuses' => \Cake\ORM\Association\BelongsTo::class,
			'Users' => \Cake\ORM\Association\BelongsTo::class,
			'Votes' => \Cake\ORM\Association\BelongsTo::class,
		])
	);

	override(
		\Cake\ORM\Table::find(0),
		map([
			'all' => \Cake\ORM\Query::class,
			'list' => \Cake\ORM\Query::class,
			'threaded' => \Cake\ORM\Query::class,
		])
	);

	override(
		\Cake\ORM\Table::hasMany(0),
		map([
			'Applications' => \Cake\ORM\Association\HasMany::class,
			'Categories' => \Cake\ORM\Association\HasMany::class,
			'DebugKit.Panels' => \Cake\ORM\Association\HasMany::class,
			'DebugKit.Requests' => \Cake\ORM\Association\HasMany::class,
			'FundingCycles' => \Cake\ORM\Association\HasMany::class,
			'Images' => \Cake\ORM\Association\HasMany::class,
			'Messages' => \Cake\ORM\Association\HasMany::class,
			'Notes' => \Cake\ORM\Association\HasMany::class,
			'Statuses' => \Cake\ORM\Association\HasMany::class,
			'Users' => \Cake\ORM\Association\HasMany::class,
			'Votes' => \Cake\ORM\Association\HasMany::class,
		])
	);

	override(
		\Cake\ORM\Table::hasOne(0),
		map([
			'Applications' => \Cake\ORM\Association\HasOne::class,
			'Categories' => \Cake\ORM\Association\HasOne::class,
			'DebugKit.Panels' => \Cake\ORM\Association\HasOne::class,
			'DebugKit.Requests' => \Cake\ORM\Association\HasOne::class,
			'FundingCycles' => \Cake\ORM\Association\HasOne::class,
			'Images' => \Cake\ORM\Association\HasOne::class,
			'Messages' => \Cake\ORM\Association\HasOne::class,
			'Notes' => \Cake\ORM\Association\HasOne::class,
			'Statuses' => \Cake\ORM\Association\HasOne::class,
			'Users' => \Cake\ORM\Association\HasOne::class,
			'Votes' => \Cake\ORM\Association\HasOne::class,
		])
	);

	override(
		\Cake\ORM\TableRegistry::get(0),
		map([
			'Applications' => \App\Model\Table\ApplicationsTable::class,
			'Categories' => \App\Model\Table\CategoriesTable::class,
			'DebugKit.Panels' => \DebugKit\Model\Table\PanelsTable::class,
			'DebugKit.Requests' => \DebugKit\Model\Table\RequestsTable::class,
			'FundingCycles' => \App\Model\Table\FundingCyclesTable::class,
			'Images' => \App\Model\Table\ImagesTable::class,
			'Messages' => \App\Model\Table\MessagesTable::class,
			'Notes' => \App\Model\Table\NotesTable::class,
			'Statuses' => \App\Model\Table\StatusesTable::class,
			'Users' => \App\Model\Table\UsersTable::class,
			'Votes' => \App\Model\Table\VotesTable::class,
		])
	);

	expectedArguments(
		\Cake\Validation\Validator::requirePresence(),
		1,
		'create',
		'update'
	);

	override(
		\Cake\View\View::element(0),
		map([
			'DebugKit.cache_panel' => \Cake\View\View::class,
			'DebugKit.deprecations_panel' => \Cake\View\View::class,
			'DebugKit.environment_panel' => \Cake\View\View::class,
			'DebugKit.history_panel' => \Cake\View\View::class,
			'DebugKit.include_panel' => \Cake\View\View::class,
			'DebugKit.log_panel' => \Cake\View\View::class,
			'DebugKit.mail_panel' => \Cake\View\View::class,
			'DebugKit.packages_panel' => \Cake\View\View::class,
			'DebugKit.preview_header' => \Cake\View\View::class,
			'DebugKit.request_panel' => \Cake\View\View::class,
			'DebugKit.routes_panel' => \Cake\View\View::class,
			'DebugKit.session_panel' => \Cake\View\View::class,
			'DebugKit.sql_log_panel' => \Cake\View\View::class,
			'DebugKit.timer_panel' => \Cake\View\View::class,
			'DebugKit.variables_panel' => \Cake\View\View::class,
			'Flash/default' => \Cake\View\View::class,
			'Flash/error' => \Cake\View\View::class,
			'Flash/success' => \Cake\View\View::class,
			'WyriHaximus/TwigView.twig_panel' => \Cake\View\View::class,
			'head' => \Cake\View\View::class,
			'navbar' => \Cake\View\View::class,
		])
	);

	override(
		\Cake\View\View::loadHelper(0),
		map([
			'Bake.Bake' => \Bake\View\Helper\BakeHelper::class,
			'Bake.DocBlock' => \Bake\View\Helper\DocBlockHelper::class,
			'Breadcrumbs' => \Cake\View\Helper\BreadcrumbsHelper::class,
			'DebugKit.Credentials' => \DebugKit\View\Helper\CredentialsHelper::class,
			'DebugKit.SimpleGraph' => \DebugKit\View\Helper\SimpleGraphHelper::class,
			'DebugKit.Tidy' => \DebugKit\View\Helper\TidyHelper::class,
			'DebugKit.Toolbar' => \DebugKit\View\Helper\ToolbarHelper::class,
			'Flash' => \Cake\View\Helper\FlashHelper::class,
			'Form' => \Cake\View\Helper\FormHelper::class,
			'Html' => \Cake\View\Helper\HtmlHelper::class,
			'IdeHelper.DocBlock' => \IdeHelper\View\Helper\DocBlockHelper::class,
			'Migrations.Migration' => \Migrations\View\Helper\MigrationHelper::class,
			'Number' => \Cake\View\Helper\NumberHelper::class,
			'Paginator' => \Cake\View\Helper\PaginatorHelper::class,
			'Rss' => \Cake\View\Helper\RssHelper::class,
			'Session' => \Cake\View\Helper\SessionHelper::class,
			'Text' => \Cake\View\Helper\TextHelper::class,
			'Time' => \Cake\View\Helper\TimeHelper::class,
			'Url' => \Cake\View\Helper\UrlHelper::class,
		])
	);

}
