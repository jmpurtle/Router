<?php
$forumDocument =
[
	"forum" => "Totally awesome forum",
	"categories" => [
		"foo" => [
			"threads" => [
				"a1" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Such thread!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Wow!",
							"votes" => 123
						]
					]
				],
				"a2" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Boring thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Low Energy!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "MAGA!",
							"votes" => 123
						]
					]
				],
				"a3" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Ditto",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Ditto",
							"votes" => 123
						]
					]
				]
			]
		],
		"bar" => [
			"threads" => [
				"b1" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						]
					]
				],
				"b2" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						]
					]
				],
				"b3" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						]
					]
				]
			]
		],
		"baz" => [
			"threads" => [
				"c1" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						]
					]
				],
				"c2" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						]
					]
				],
				"c3" => [
					"posts" => [
						"post1" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post2" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						],
						"post3" => [
							"timestamp" => 3523623,
							"body" => "Neat thread!",
							"votes" => 123
						]
					]
				]
			]
		]
	]
];

echo "<pre>" . json_encode($forumDocument) . "</pre>";