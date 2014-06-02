<?php

namespace Tools\Cloudinary;

use \Consts as C;
use \Tools as T;

/**
 * @author Abbas Ali Hashemian <info@namedin.com> <tondarweb@gmail.com> http://webdesignir.com
 * @package Odinid
 * @version 1
 * @copyright (c) Odinid
 * @access public
 */
class Cloudinary {

	static function Load($Uploader = false) {
//		\html::Cloudinary_Load();
		static $Configured = false;
		require_once 'src/Cloudinary.php';
		require_once 'src/Api.php';
		if ($Uploader)
			require_once 'src/Uploader.php';
		if (!$Configured) {
			\Cloudinary::config(\Conf::CloudinaryConfig());
			$Configured = true;
		}
	}

	/**
	 * You can upload images and any other files from your PHP server.
	 *  Uploading is done over HTTPS using a secure protocol based on the api_key and api_secret parameters you provide.
	 * The following command uploads a local file to Cloudinary:
	 * \Cloudinary\Uploader::upload("/home/my_image.jpg")
	 * @param type $file
	 * The resource to upload. Can be one of the following:
	 * <ul>
	 * <li>A local path (e.g., '/home/my_image.jpg').</li>
	 * <li>An HTTP URL of a resource available on the Internet (e.g., 'http://www.example.com/image.jpg').</li>
	 * <li>A URL of a file in a private S3 bucket white-listed for your account (e.g., 's3://my-bucket/my-path/my-file.jpg')</li>
	 * </ul>
	 * @param array $options
	 * @return array upload result
	 * Each image uploaded to Cloudinary is assigned a unique Public ID and is available for
	 *  immediate delivery and transformation.
	 *  The upload method returns an associative array with content similar to
	 *  that shown in the following example:
	 * <pre>
	 * Array
	 * (
	 * 	[public_id] => sample
	 * 	[version] => 1312461204
	 * 	[width] => 864
	 * 	[height] => 576
	 * 	[format] => jpg
	 * 	[bytes] => 120253
	 * 	[url] => http://res.cloudinary.com/demo/image/upload/v1371281596/sample.jpg
	 * 	[secure_url] => https://res.cloudinary.com/demo/image/upload/v1371281596/sample.jpg
	 * )
	 * </pre>
	 */
	public static function Uplaod($file, $options = array()) {
		self::Load(true);
		return \Cloudinary\Uploader::upload($file, $options);
	}

	public static function Destroy($file, $options = array()) {
		self::Load(true);
		return \Cloudinary\Uploader::destroy($file, $options);
	}

	public static function cl_image_tag($file, $options = array()) {
		self::Load();
		return cl_image_tag($file, $options);
	}
	
//	public static function GetNewUniqueID() {
//		return uniqid();
//	}

//	public static function GetTheUploadID(
//	$UploadPath, $Prefix = null, $DBPhotoField = 'Picture', $fncGetDatarow = null
//	, $GenerateNewOne = false, &$UniqueKey = null, $Refresh = false) {
//		if ($GenerateNewOne)
//			$UniqueKey = uniqid(); //reference
//		else {
//			$dr = $fncGetDatarow ? $fncGetDatarow($Refresh) : null;
//			if (!$dr || !$dr[$DBPhotoField])
//				return null;
//		}
//		return $UploadPath . $Prefix . '_' . ($GenerateNewOne ? $UniqueKey : $dr[$DBPhotoField]);
//	}

//	/**
//	 * 
//	 * @param type $source
//	 * @param type $options
//	 * @return string
//	 */
//	public static function img($source, $options = array()) {
//		return cl_image_tag($source, $options);
//	}
//
//	/**
//	 * @return Cloudinary_UploadConfig
//	 */
//	static function UploadConfigInstance() {
//		return new Cloudinary_UploadConfig();
//	}
//
}

//
///**
// * @method type methodName(type $paramName) Description
// * 
// * @method Cloudinary_UploadConfig file($file)
// *  The resource to upload. Can be one of the following:
// * <ul>
// *     <li>A local path (e.g., '/home/my_image.jpg').</li>
// *     <li>An HTTP URL of a resource available on the Internet (e.g., 'http://www.example.com/image.jpg').</li>
// *     <li>A URL of a file in a private S3 bucket white-listed for your account (e.g., 's3://my-bucket/my-path/my-file.jpg')</li>
// * </ul>
// * @method Cloudinary_UploadConfig public_id($public_id)
// *  (Optional) - Public ID to assign to the uploaded image. Random ID is generated otherwise and returned as a result for this call.
// * @method Cloudinary_UploadConfig tags($tags)
// *  (Optional) - A tag name or an array with a list of tag names to assign to the uploaded image.
// * @method Cloudinary_UploadConfig format($format)
// *  (Optional) - A format to convert the uploaded image to before saving in the cloud. For example: 'jpg'.
// * @method Cloudinary_UploadConfig Transformation($Transformation)
// *  parameters (Optional) - Any combination of transformation-related parameters for transforming the uploaded image before storing in the cloud. For example: :width, :height, :crop, :gravity, :quality, :transformation.
// * @method Cloudinary_UploadConfig eager($eager)
// *  (Optional) - A list of transformations to generate for the uploaded image during the upload process, instead of lazily creating these on-the-fly on access.
// * @method Cloudinary_UploadConfig eager_async($eager_async)
// *  (Optional, Boolean) - Whether to generate the eager transformations asynchronously in the background after the upload request is completed rather than online as part of the upload call. Default: false.
// * @method Cloudinary_UploadConfig resource_type($resource_type)
// *  (Optional) - Valid values: 'image', 'raw' and 'auto'. Default: 'image'.
// * @method Cloudinary_UploadConfig type($type)
// *  (Optional) - Allows uploading images as 'private' or 'authenticated'. Valid values: 'upload', 'private' and 'authenticated'. Default: 'upload'.
// * @method Cloudinary_UploadConfig headers($headers)
// *  (Optional) - An HTTP header or an array of headers for returning as response HTTP headers when delivering the uploaded image to your users. Supported headers: 'Link', 'X-Robots-Tag'. For example 'X-Robots-Tag: noindex'.
// * @method Cloudinary_UploadConfig callback($callback)
// *  (Optional) - An HTTP URL to redirect to instead of returning the upload response. Signed upload result parameters are added to the callback URL. Ignored if it is an XHR upload request (Ajax XMLHttpRequest).
// * @method Cloudinary_UploadConfig notification_url($notification_url)
// *  (Optional) - An HTTP URL to send notification to (a webhook) when the upload is completed.
// * @method Cloudinary_UploadConfig eager_notification_url($eager_notification_url)
// *  (Optional) - An HTTP URL to send notification to (a webhook) when the generation of eager transformations is completed.
// * @method Cloudinary_UploadConfig backup($backup)
// *  (Optional, Boolean) - Tell Cloudinary whether to backup the uploaded image. Overrides the default backup settings of your account.
// * @method Cloudinary_UploadConfig faces($faces)
// *  (Optional, Boolean) - Whether to retrieve a list of coordinates of automatically detected faces in the uploaded photo. Default: false.
// * @method Cloudinary_UploadConfig exif($exif)
// *  (Optional, Boolean) - Whether to retrieve the Exif metadata of the uploaded photo. Default: false.
// * @method Cloudinary_UploadConfig colors($colors)
// *  (Optional, Boolean) - Whether to retrieve predominant colors & color histogram of the uploaded image. Default: false.
// * @method Cloudinary_UploadConfig image_metadata($image_metadata)
// *  (Optional, Boolean) - Whether to retrieve IPTC and detailed Exif metadata of the uploaded photo. Default: false.
// * @method Cloudinary_UploadConfig invalidate($invalidate)
// *  (Optional, Boolean) - Whether to invalidate CDN cache copies of a previously uploaded image that shares the same public ID. Default: false.
// * @method Cloudinary_UploadConfig use_filename($use_filename)
// *  (Optional, Boolean) - Whether to use the original file name of the uploaded image if available for the public ID. The file name is normalized and random characters are appended to ensure uniqueness. Default: false.
// * @method Cloudinary_UploadConfig unique_filename($unique_filename)
// *  (Optional, Boolean) - Only relevant if use_filename is true. When set to false, should not add random characters at the end of the filename that guarantee its uniqueness. Default: true.
// * @method Cloudinary_UploadConfig overwrite($overwrite)
// *  (Optional, Boolean) - Whether to overwrite existing resources with the same public ID. When set to false, return immediately if a resource with the same public ID was found. Default: true.
// * @method Cloudinary_UploadConfig discard_original_filename($discard_original_filename)
// *  (Optional, Boolean) - Whether to discard the name of the original uploaded file. Relevant when delivering images as attachments (setting the 'flags' transformation parameter to 'attachment'). Default: false.
// * 
// */
//class Cloudinary_UploadConfig extends \Base\ConfigArray {
//	
//}
//
////class Upload
