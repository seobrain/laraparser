<?php

namespace Seobrain\Laraparser;

use Exception;
use Illuminate\Support\Facades\Http;

/**
 * Class Laraparser
 * @package Seobrain\Laraparser
 */
class Laraparser
{
    /**
     * @var string $host The base URL to use for calls to the API
     */
    private string $host;

    /**
     * @var string $password The password for the A-Parser API
     */
    private string $password;

    /**
     * @param string $host
     * @param string $password
     */
    public function __construct(string $host = '', string $password = '')
    {
        $this->host = $host ?: config('aparser.host');
        $this->password = $password ?: config('aparser.password');
    }

    /**
     * Check of work the server
     *
     * @return string
     * @throws Exception
     */
    public function ping()
    {
        return $this->apiCall('ping');
    }

    /**
     * General information and list of all available parsers
     *
     * @return array
     * @throws Exception
     */
    public function info()
    {
        return $this->apiCall('info');
    }

    /**
     * The single request for parsing, can be used any parser and preset.
     * As a result there will be a created string in compliance with a result format,
     * the given in a preset, and also full log of work parser.
     *
     * @param string $query
     * @param string $parser
     * @param string $preset
     * @param int $rawResults
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function oneRequest(string $query, string $parser, $preset = 'default', $rawResults = 0, $options = [])
    {
        return $this->apiCall('oneRequest', [
            'query' => $query,
            'parser' => $parser,
            'preset' => $preset,
            'rawResults' => $rawResults,
            'options' => $options
        ]);
    }

    /**
     * The mass request for parsing, can be used any parser and preset,
     * and is also specified in what quantity of threads to make parsing.
     * As a result there will be a created string in compliance with a result format,
     * the given in a preset, and also full log of work parser on each thread.
     *
     * @param array $queries
     * @param string $parser
     * @param string $preset
     * @param int $threads
     * @param int $rawResults
     * @param array $options
     * @return array
     * @throws Exception
     */
    public function bulkRequest(array $queries, string $parser, $preset = 'default', $threads = 5, $rawResults = 0, $options = [])
    {
        return $this->apiCall('bulkRequest', [
            'queries' => $queries,
            'parser' => $parser,
            'preset' => $preset,
            'threads' => $threads,
            'rawResults' => $rawResults,
            'options' => $options,
        ]);
    }

    /**
     * Receiving settings of the specified parser and preset.
     *
     * @param $parser
     * @param string $preset
     * @return array
     * @throws Exception
     */
    public function getParserPreset($parser, $preset = 'default')
    {
        return $this->apiCall('getParserPreset', [
            'parser' => $parser,
            'preset' => $preset,
        ]);
    }

    /**
     * Request of list live proxy. Returns a list of alive proxy from all checkers.
     *
     * @return array
     * @throws Exception
     */
    public function getProxies()
    {
        return $this->apiCall('getProxies');
    }

    /**
     * Add a task to turn all options are similar to those that are
     * specified in the interface Add Task
     *
     * @param string $configPreset
     * @param string $taskPreset
     * @param array $queries
     * @param array $options
     * @return string taskUid
     * @throws Exception
     */
    public function addTask($configPreset = 'default', $taskPreset = 'default', $queries = [], $options = [])
    {
        $defaults = [
            'preset' => $taskPreset,
            'configPreset' => $configPreset,
            'parsers' => [],
            'resultsFormat' => '',
            'resultsSaveTo' => 'file',
            'resultsFileName' => '$datefile.format().txt',
            'additionalFormats' => [],
            'resultsUnique' => 'no',
            'queriesFrom' => 'text',
            'queryFormat' => ['$query'],
            'uniqueQueries' => 0,
            'saveFailedQueries' => 0,
            'resultsOptions' => [],
            'doLog' => 'no',
            'limitLogsCount' => '0',
            'keepUnique' => 'No',
            'moreOptions' => 0,
            'resultsPrepend' => '',
            'resultsAppend' => '',
            'queryBuilders' => [],
            'resultsBuilders' => [],
            'configOverrides' => [],
            'runTaskOnComplete' => null,
            'useResultsFileAsQueriesFile' => 0,
            'runTaskOnCompleteConfig' => 'default',
            'toolsJS' => '',
            'prio' => 5,
            'removeOnComplete' => 0,
            'callURLOnComplete' => '',
            'queries' => $queries,
        ];

        $data = array_merge($defaults, $options);

        return $this->apiCall('addTask', $data);
    }

    /**
     * Receiving a status of task on its id
     *
     * @param int $taskUid
     * @return array
     * @throws Exception
     */
    public function getTaskState(int $taskUid)
    {
        return $this->apiCall('getTaskState', [
            'taskUid' => $taskUid
        ]);
    }

    /**
     * Receiving a configuration of task on its id
     *
     * @param int $taskUid
     * @return array
     * @throws Exception
     */
    public function getTaskConf(int $taskUid)
    {
        return $this->apiCall('getTaskConf', [
            'taskUid' => $taskUid
        ]);
    }

    /**
     * Obtaining the link for downloading of result on task id
     *
     * @param int $taskUid
     * @return array
     * @throws Exception
     */
    public function getTaskResultsFile(int $taskUid)
    {
        return $this->apiCall('getTaskResultsFile', [
            'taskUid' => $taskUid
        ]);
    }

    /**
     * Deleting file of result on task id
     *
     * @param int $taskUid
     * @return mixed
     * @throws Exception
     */
    public function deleteTaskResultsFile(int $taskUid)
    {
        return $this->apiCall('deleteTaskResultsFile', [
            'taskUid' => $taskUid
        ]);
    }

    /**
     * Change of the status task on its id
     * There are only 4 statuses to which it is possible to transfer task:
     * starting, pausing, stopping, deleting
     *
     * @param int $taskUid
     * @param string $toStatus starting|pausing|stopping|deleting
     * @return array
     * @throws Exception
     */
    public function changeTaskStatus(int $taskUid, string $toStatus)
    {
        return $this->apiCall('changeTaskStatus', [
            'taskUid' => $taskUid,
            'toStatus' => $toStatus
        ]);
    }

    /**
     * Relocation of task in queue on its id
     * Possible directions of relocation:
     * start, end, up, down
     *
     * @param int $taskUid
     * @param string $direction start|end|up|down
     * @return array
     * @throws Exception
     */
    public function moveTask(int $taskUid, string $direction)
    {
        return $this->apiCall('moveTask', [
            'taskUid' => $taskUid,
            'direction' => $direction
        ]);
    }

    /**
     * Gets a list of active tasks.
     * If you send an optional parameter completed: 1, we get a list of completed tasks.
     *
     * @param $completed 1
     * @return mixed
     * @throws Exception
     */
    public function getTasksList($completed = 0)
    {
        return $this->apiCall('getTasksList', [
            'completed' => $completed
        ]);
    }

    /**
     * Displays a list of all available results that can return the specified parser.
     *
     * @param string $parser
     * @return array
     * @throws Exception
     */
    public function getParserInfo(string $parser)
    {
        return $this->apiCall('getParserInfo', [
            'parser' => $parser
        ]);
    }

    /**
     * Update executable file of the parser to the latest version,
     * after sending the command A-Parser will be automatically restarted.
     * API returns a response about the success after download and update the executable file,
     * it may take 1-3 minutes.
     *
     * @return mixed
     * @throws Exception
     */
    public function update()
    {
        return $this->apiCall('update');
    }

    /**
     * Getting the number of active accounts (for Yandex)
     *
     * @return mixed
     * @throws Exception
     */
    public function getAccountsCount()
    {
        return $this->apiCall('getAccountsCount');
    }

    /**
     * API call execution
     *
     * @param string $action
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function apiCall(string $action, array $data = [])
    {
        $request = [
            'action' => $action,
            'password' => $this->password,
            'data' => $data
        ];

        $response = Http::post($this->host, $request)->throw();
        if ($response->successful() && @$response->json()['success']) {
            return @$response->json()['data'] ?: true;
        } else {
            throw new Exception( @$response->json()['msg'] ?: 'unknown error');
        }
    }
}
