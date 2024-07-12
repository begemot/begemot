<?php

/**
 * 
 * Модель данных, которая представляет с собой линейный массив, но содержит в себе всю информацию 
 * для быстрого построения иерархического меню. 
 * 
 * В конструктор нужно передать массив данных вида 
 * 
 * $data = [
 *     ['id' => 1, 'order' => 1, 'level' => 0, 'name' => 'name_1', 'pid' => -1],
 *     ['id' => 3, 'order' => 2, 'level' => 1, 'name' => 'name_3', 'pid' => 1],
 *     ['id' => 9, 'order' => 3, 'level' => 2, 'name' => 'name_9', 'pid' => 3],
 *     ['id' => 11, 'order' => 4, 'level' => 2, 'name' => 'name_11', 'pid' => 3],
 *     ['id' => 5, 'order' => 5, 'level' => 1, 'name' => 'name_5', 'pid' => 1],
 *     ['id' => 15, 'order' => 6, 'level' => 2, 'name' => 'name_15', 'pid' => 5],
 *     ['id' => 17, 'order' => 7, 'level' => 2, 'name' => 'name_17', 'pid' => 5],
 *     ['id' => 2, 'order' => 8, 'level' => 0, 'name' => 'name_2', 'pid' => -1],
 *     ['id' => 6, 'order' => 9, 'level' => 1, 'name' => 'name_6', 'pid' => 2],
 *     ['id' => 18, 'order' => 10, 'level' => 2, 'name' => 'name_18', 'pid' => 6],
 *     ['id' => 20, 'order' => 11, 'level' => 2, 'name' => 'name_20', 'pid' => 6],
 *     ['id' => 8, 'order' => 12, 'level' => 1, 'name' => 'name_8', 'pid' => 2],
 *     ['id' => 4, 'order' => 13, 'level' => 0, 'name' => 'name_4', 'pid' => -1],
 *     ['id' => 12, 'order' => 14, 'level' => 1, 'name' => 'name_12', 'pid' => 4],
 *     ['id' => 14, 'order' => 15, 'level' => 1, 'name' => 'name_14', 'pid' => 4],
 *     ['id' => 7, 'order' => 16, 'level' => 0, 'name' => 'name_7', 'pid' => -1],
 *     ['id' => 10, 'order' => 17, 'level' => 0, 'name' => 'name_10', 'pid' => -1],
 *     ['id' => 13, 'order' => 18, 'level' => 0, 'name' => 'name_13', 'pid' => -1],
 *     ['id' => 16, 'order' => 19, 'level' => 0, 'name' => 'name_16', 'pid' => -1],
 *     ['id' => 19, 'order' => 20, 'level' => 0, 'name' => 'name_19', 'pid' => -1],
 * ];
 * 
 * 
 * Просто отсортировав массив данных по order мы получаем список в порядке, 
 * в котором он бы получился, если бы мы строили рекурсивно список по вложенному массиву 
 * или методом обхода дочерних элементов. 
 * 
 * Операции вставки до, после и прикрепления поддеревьев учитывает целостность струкруты. 
 * 
 * 
 */

class FlatTreeModel
{

    private $data;

    public function getData()
    {
        return $this->data;
    }

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function printHierarchicalList($parentId = -1, $level = 0)
    {
        $this->sortDataByOrder($this->data);
        $hasChildren = false;

        foreach ($this->data as $item) {
            if ($item['pid'] == $parentId) {
                if (!$hasChildren) {
                    $hasChildren = true;
                    echo str_repeat("\t", $level) . "<ul>\n";
                }

                echo str_repeat("\t", $level + 1) . "<li>" . htmlspecialchars($item['name'] . ' ' . $item['order']) . "</li>\n";

                $this->printHierarchicalList($item['id'], $level + 1);
            }
        }

        if ($hasChildren) {
            echo str_repeat("\t", $level) . "</ul>\n";
        }
    }

    /**
     * Удаляет из набора элемент и все его дочерние элементы по pid
     * элемента elementId
     */
    function deleteElementWithChildren(&$data, $elementId)
    {

        // Get all children of the element
        $children = $this->getChildren($elementId);

        // Remove the element and its children from the array
        $data = array_values(array_filter($data, function ($item) use ($elementId, $children) {
            return $item['id'] != $elementId && !in_array($item['id'], $children);
        }));
        $this->sortDataByOrder($data);
        // Update the 'order' of the remaining elements
        foreach ($data as $index => &$item) {
            $item['order'] = $index + 1;
        }
    }

    /**
     * возвращает массив id всех дочерних элементов по всей вложенности
     */
    function getChildren($parentId)
    {

        $children = [];
        foreach ($this->data as $item) {
            if ($item['pid'] == $parentId) {
                $children[] = $item['id'];
                $children = array_merge($children, $this->getChildren($this->data, $item['id']));
            }
        }
        return $children;
    }


    /**
     * Выполняется сортировка всех элементов по order
     * и переназначение order, на случай если где-то произошел пропуск порядка order
     */
    function sortDataByOrder(&$data)
    {
        usort($data, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        // foreach ($data as $index => &$item) {
        // $item['order'] = $index + 1;
        // }
    }

    /**
     *
     * Возвращает поддерево вместе с указанным элементом
     *
     */

    function getSubTree( $elementId)
    {
        $data = $this->data;
        // Helper function to recursively get children
        $getChildren = function ($data, $parentId) use (&$getChildren) {
            $children = [];
            foreach ($data as $item) {
                if ($item['pid'] == $parentId) {
                    $children[] = $item;
                    $children = array_merge($children, $getChildren($data, $item['id']));
                }
            }
            return $children;
        };

        // Find the root element
        $subTree = [];
        foreach ($data as $item) {
            if ($item['id'] == $elementId) {
                $subTree[] = $item;
                $subTree = array_merge($subTree, $getChildren($data, $item['id']));
                break;
            }
        }

        return $subTree;
    }

    function getNormalizedSubTree($data, $elementId)
    {
        $data = $this->data;
        $subTree = $this->getSubTree( $elementId);

        return $this->normalizeSubTree($subTree);
    }


    /**
     * Нормализует поддерево, к дереву. Все корневые элементы становяться pid=-1,
     * меняется level и ордер так, как буд-то это отдельное дерево.
     *
     */
    function normalizeSubTree($subTree)
    {
        if (empty($subTree)) {
            return $subTree;
        }

        // Find the minimum level in the subTree
        $minLevel = min(array_column($subTree, 'level'));

        // Normalize levels and update pid for level 0 elements
        foreach ($subTree as &$item) {
            $item['level'] -= $minLevel;
            if ($item['level'] == 0) {
                $item['pid'] = -1;
            }
        }

        // Update the order to reflect the new positions
        usort($subTree, function ($a, $b) {
            return $a['order'] <=> $b['order'];
        });

        foreach ($subTree as $index => &$item) {
            $item['order'] = $index + 1;
        }

        return $subTree;
    }

    /**
     * Прикрепляет поддерево к другому элементу как дочернее путем замены pid,
     * ничего не делает с level и order
     */
    function attachSubTree(&$data, $parentId, $subTree)
    {

        foreach ($subTree as &$item) {

            if ($item['pid'] == -1) $item['pid'] = $parentId;
        }

        // Add the normalized subtree to the data
        $data = array_merge($data, array_values($subTree));

        // Update the order of all elements in the main data
        // sortDataByOrder($data);
    }

    /**
     * Проходит рекурсивно по дереву используя исключительно pid
     * и проставляет level и order заново
     *
     */
    function updateOrderAndLevel(&$data)
    {
        $this->updateOrder($data);
        $this->updateLevel($data);
    }

    function updateOrder(&$data)
    {
        // Create a mapping from id to item for quick access
        $itemsById = [];
        foreach ($data as &$item) {
            $itemsById[$item['id']] = &$item;
        }

        // Initialize order counter
        $orderCounter = 1;



        // Start updating from root elements (pid = -1)
        $this->updateRecursivelyOrder($itemsById, $orderCounter, -1, 0);
    }

    // Recursive function to update order and level
    function updateRecursivelyOrder(&$itemsById, &$orderCounter, $parentId, $currentLevel)
    {
        foreach ($itemsById as &$item) {
            if ($item['pid'] == $parentId) {
                // Update order and level
                $item['order'] = $orderCounter++;


                // Recur for children
                $this->updateRecursivelyOrder($itemsById, $orderCounter, $item['id'], $currentLevel + 1);
            }
        }
    }

    function updateLevel(&$data)
    {
        // Create a mapping from id to item for quick access
        $itemsById = [];
        foreach ($data as &$item) {
            $itemsById[$item['id']] = &$item;
        }

        // Initialize order counter
        $orderCounter = 1;



        // Start updating from root elements (pid = -1)
        $this->updateRecursivelyLevel($itemsById, $orderCounter, -1, 0);
    }

    // Recursive function to update order and level
    function updateRecursivelyLevel(&$itemsById, &$orderCounter, $parentId, $currentLevel)
    {
        foreach ($itemsById as &$item) {
            if ($item['pid'] == $parentId) {
                // Update order and level
                $item['level'] = $currentLevel;


                // Recur for children
                $this->updateRecursivelyLevel($itemsById, $orderCounter, $item['id'], $currentLevel + 1);
            }
        }
    }

    /**
     * Прикрепляем как дочернее елемент вместе с дочерними к другому элементу.
     * Должен корректно обрабатывать order
     */
    function attachOneToAnother($firstId, $targetId)
    {

        $subtree = $this->getNormalizedSubTree($this->data, $firstId);
        $this->deleteElementWithChildren($this->data, $firstId);
        $targetSubTree = $this->getSubTree( $targetId);
        // Находим минимальное и максимальное значение order
        $orders = array_column($targetSubTree, 'order');
        $minTagetOrder = min($orders);
        $maxTargetOrder = max($orders);
        $this->increaseTreeOrder($subtree, $maxTargetOrder);
        $this->spareOrder($this->data, $maxTargetOrder, count($subtree));
        $this->attachSubTree($this->data, $targetId, $subtree);

        $this->updateOrderAndLevel($this->data);
    }

    /**
     * Переносим элемент вместе с дочерними после $targetId
     */
    function insertSubTreeAfter($firstId, $targetId)
    {

        $subtree = $this->getNormalizedSubTree($this->data, $firstId);
        $this->deleteElementWithChildren($this->data, $firstId);
        $targetElement = $this->findElementById($this->data, $targetId);
        $parentId = $targetElement['pid'];



        $targetSubTree = $this->getSubTree( $targetId);

        // Находим минимальное и максимальное значение order
        $orders = array_column($targetSubTree, 'order');
        // $minTagetOrder = min($orders);
        $maxTargetOrder = max($orders);
        $this->increaseTreeOrder($subtree, $maxTargetOrder);
        $this->spareOrder($this->data, $maxTargetOrder, count($subtree));

        $this->attachSubTree($this->data, $parentId, $subtree);

        // updateOrderAndLevel($data);
    }


    /**
     * Переносим элемент вместе с дочерними после $targetId
     */
    function insertSubTreeBefore($firstId, $targetId)
    {
        $subtree = $this->getNormalizedSubTree($this->data, $firstId);
        $this->deleteElementWithChildren($this->data, $firstId);
        $targetElement = $this->findElementById($this->data, $targetId);
        $parentId = $targetElement['pid'];


        $targetSubTree = $this->getSubTree( $targetId);

        // Находим минимальное и максимальное значение order
        $orders = array_column($targetSubTree, 'order');
        $minTagetOrder = min($orders);
        $maxTargetOrder = max($orders);
        $this->increaseTreeOrder($subtree, $minTagetOrder - 1);
        $this->spareOrder($this->data, $minTagetOrder - 1, count($subtree));

        $this->attachSubTree($this->data, $parentId, $subtree);

        // updateOrderAndLevel($data);
    }

    function increaseTreeOrder(&$tree, $orderInc)
    {
        foreach ($tree as &$treeElem) {
            $treeElem['order'] += $orderInc;
        }
    }

    function spareOrder(&$data, $orderFrom, $orderIncrease)
    {
        foreach ($data as $key => $item) {
            if ($item['order'] > $orderFrom) {
                $data[$key]['order'] += $orderIncrease;
            }
        }
    }

    function getParent($data, $targetId)
    {
        $element = $this->findElementById($data, $targetId);
        return $parent = $this->findElementById($data, $element['pid']);
    }

    function findElementById($data, $id)
    {
        $index = array_search($id, array_column($data, 'id'));
        if ($index !== false) {
            return $data[$index];
        }
        return null; // Возвращаем null, если элемент не найден
    }
}