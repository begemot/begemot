function each(arr, callback) {
    var length = arr.length;
    var i;

    for (i = 0; i < length; i++) {
        callback.call(arr, arr[i], i, arr);
    }

    return arr;
}


app.directive('crop', ['$http',
    function ($http) {
        return {
            restrict: 'E',

            template: '<div style="width: 600px;height:400px;display:block;margin:0 auto;"><img src="{{imageSrc}}" /></div>',
            scope: {
                imageSrc: '@imageSrc',
                allDataCollection: '=',
                imageId:'@',
                activeFilter: '=',
                blobSendHook: '=',
                images:'=',
                subGallery:'=',


            },

            link: function (scope, iElement, iAttrs, controller) {
                console.log(scope.allDataCollection);

                imageDomObj = $(iElement).find('img')[0]


                var previews = document.querySelectorAll('.preview');
                var previewReady = false;

                imageDomObj.onload = onLoadFunction = function () {
                    if (scope.activeFilter==null) return;
                    if (_.isObject(scope.cropperObj))
                        scope.cropperObj.destroy()
                    console.log(scope.activeFilter);
                    scope.cropperObj =
                        new Cropper(imageDomObj, {
                            viewMode: 0,
                            responsive: true,
                            aspectRatio: scope.activeFilter.width / scope.activeFilter.height,
                            minContainerWidth: 600,
                            minContainerHeight: 400,
                            ready: function () {
                                var clone = this.cloneNode();

                                clone.className = '';
                                clone.style.cssText = (
                                    'display: block;' +

                                    'min-width: 0;' +
                                    'min-height: 0;' +
                                    'max-width: none;' +
                                    'max-height: none;'
                                );

                                each(previews, function (elem) {
                                    elem.innerHTML = '';
                                    elem.style.cssText = (
                                        'width:' + scope.activeFilter.width + 'px;' +
                                        'height:' + scope.activeFilter.height + 'px;' +
                                        'display: block;overflow: hidden;'
                                    );
                                    elem.appendChild(clone.cloneNode());
                                    scope.cropperObj.reset();
                                });
                                previewReady = true;
                                scope.cropperObj.reset();
                            },

                            crop: function (event) {
                                if (!previewReady) {
                                    return;
                                }

                                var data = event.detail;
                                var cropper = this.cropper;
                                var imageData = cropper.getImageData();
                                // console.log(data);
                                var previewAspectRatio = data.width / data.height;

                                each(previews, function (elem) {
                                    var previewImage = elem.getElementsByTagName('img').item(0);
                                    var previewWidth = elem.offsetWidth;
                                    var previewHeight = previewWidth / previewAspectRatio;
                                    var imageScaledRatio = data.width / previewWidth;

                                    // elem.style.height = previewHeight + 'px';
                                    previewImage.style.width = imageData.naturalWidth / imageScaledRatio + 'px';
                                    previewImage.style.height = imageData.naturalHeight / imageScaledRatio + 'px';
                                    previewImage.style.marginLeft = -data.x / imageScaledRatio + 'px';
                                    previewImage.style.marginTop = -data.y / imageScaledRatio + 'px';
                                });


                            },
                        })
                }
                this.cropperInit = onLoadFunction

                scope.$watch('activeFilter.name', function () {
                    onLoadFunction()
                });

                scope.blobSendHook = function () {
                    console.log(scope.imageId)
                    scope.galId = iAttrs.galleryId;
                    scope.id = iAttrs.id;
                    image = _.find(scope.images, {id: scope.imageId})

                    scope.cropperObj.getCroppedCanvas().toBlob((blob) => {
                        console.log(blob)
                        const formData = new FormData();


                        formData.append('croppedImage', blob/*, 'example.png' */);
                        console.log({
                            gallery:scope.galId,
                            id:scope.id,
                            imageId:scope.imageId,
                            filterName:scope.activeFilter.name,
                            subGallery:scope.subGallery
                        });
                        $http({
                            url:'/pictureBox/api/savePreviewImage',
                            method: 'POST',
                            data: formData,
                            headers: {'Content-Type': undefined},
                            params:{
                                gallery:scope.galId,
                                id:scope.id,
                                imageId:scope.imageId,
                                filterName:scope.activeFilter.name,
                                subGallery:scope.subGallery
                            }
                        }).then(()=>{
                            each(previews, function (elem) {


                                img = angular.element(elem).parent().find('#realPreview')

                                img.attr('src',img.attr('src')+'?'+_.random(1000))
                                console.log(img.attr('src'));
                            })
                            console.log(scope.allDataCollection);
                            console.log(scope.allDataCollection[scope.subGallery]);
                            scope.allDataCollection[scope.subGallery].getData();
                            //cope.imagesReload();

                        })

                    });

                }


            }
        }
    }

])