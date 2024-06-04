angular.module('modules', []).service('modulesService', [
	'$http',
	'$q',
	function ($http, $q) {
		var modulesList = null
		var modulesDataList = null

		this.loadData = function () {
			var promiseModulesList = $http({
				method: 'GET',
				url: '/modules/default/getModulesList',
			})
			var promiseModulesDataList = $http({
				method: 'GET',
				url: '/modules/default/getModulesDataList',
			})

			return $q
				.all([promiseModulesList, promiseModulesDataList])
				.then(result => {
					modulesList = result[0].data
					modulesDataList = result[1].data
				})
		}

		this.getModulesList = () => {
			return modulesList
		}

		this.getActiveModulesData = () => {
			console.log(modulesDataList)
			const activeModules = Object.keys(modulesDataList).filter(
				key => modulesDataList[key].active
			)
			return activeModules
		}

		this.getModulesData = () => {
			return modulesDataList
		}

		// factory function body that constructs shinyNewServiceInstance
	},
])
