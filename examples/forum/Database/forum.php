<?php
return [
	'forum',
	'categories' => [
		'foo' => [
			'id' => 'C1',
			'threads' => [
				'thread1' => [
					'id' => 'T1',
					'posts' => [
						'post1' => [
							'id'     => 'P1',
							'author' => 'Galahad',
							'body'   => 'What a strange person.',
							'votes'  => '5',
							'flag'   => [
								'locked' => false,
								'sticky' => false,
								'hidden' => false
							],
							'children' => [
								'child1' => [
									'id'     => 'P2',
									'author' => 'KingArthur',
									'body'   => 'Now look here, my good ma--',
									'votes'  => '13',
									'flag'   => [
										'locked' => false,
										'sticky' => false,
										'hidden' => false
									],
									'children' => [
										'child1' => [
											'id'     => 'P3',
											'author' => 'FrenchSoldier',
											'body'   => "Ah don' wanna talk to you no more, you empty-headed animal food-trough wiper! Ah fart in your general direction! Your mother was a hamster, and your father smelt of elderberries!",
											'votes'  => '1343',
											'flag'   => [
												'locked' => false,
												'sticky' => false,
												'hidden' => false
											],
											'children' => [
												
											]
										]
									]
								]
							]
						]
					]
				]
			]
		]
	]
];