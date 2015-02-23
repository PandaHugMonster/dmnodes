# dmnodes
PHP Data Mining библиотеки

Был сделан базовый коммит. Без оптимизации или улучшений.
Некоторые улучшения будут добавлено скоро.

Только алгоритм K-Means пока реализован в DMNodes (KMP)

Структура директорий:
	* DBstore - Библиотеки работы с источниками. 
	* includes - Простые файлы подключения необходимых файлов для конкретного алгоритма
	* samples - Примеры в формате text/plain
	* Utilities - Некоторые дополнительные бибилотеки
	
Данная версия библиотек содержит только поддержку источника TxtTemplate.
Поддержка других источников будет добавлена позже.

Код используемый в run.php:

	// Задаётся текущая папка в константу HERE
	define(HERE, __DIR__);

	// Подключить всё необходимое для алгоритма K-Means
	include 'includes/kmp.include';
	
	// Создание Объекта-Источника (TxtTemplate)
	$store = new StoreText('TxtTemplate');
	// Получение данных из источника (файл, база данных, поток данных)
	$store->retrieveData('samples/weightheight.txt');
	// Создание Объекта-Алгоритма и предоставить данные из 
	// Объекта-Источника (будет запрещено в следующих версиях)
	$tree = new KMP($store->toArray());
	// Анализировать данные и получив результирубщий массив распечатать его
	print_r($tree->buildMap());

Имейте ввиду что данная версия имеет чисто академические базовые функции.
С версии 0.1.0 библиотеки и алгоритм станут более эффективными и появится возможность работы с BigData.

Текущая версия алгоритма использует память, поэтому в данной 
версии невозможно использовать данные BigData (Превышающие объём оперативной памяти)

All good improvements from 0.1.0
