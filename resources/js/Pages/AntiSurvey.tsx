import Selector from "@/components/SelectSearch";
import { Button } from "@/components/ui/button";
import { Form, FormField, FormMessage } from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from "@/components/ui/select";
import {
	Sheet,
	SheetContent,
	SheetDescription,
	SheetHeader,
	SheetTitle,
	SheetTrigger,
} from "@/components/ui/sheet";
import { Toaster } from "@/components/ui/toaster";
import { useToast } from "@/hooks/use-toast";
import { zodResolver } from "@hookform/resolvers/zod";
import { Head } from "@inertiajs/react";
import axios from "axios";
import { useState } from "react";
import { useForm } from "react-hook-form";
import { z } from "zod";

const formSchema = z.object({
	customerId: z
		.string({ message: "ID Customer tidak boleh kosong" })
		.nonempty("ID Customer tidak boleh kosong"),
	driverId: z
		.string({ message: "NIK Supir tidak boleh kosong" })
		.nonempty("NIK Supir tidak boleh kosong"),
	answerId: z
		.string({ message: "Alasan tidak boleh kosong" })
		.nonempty("Alasan tidak boleh kosong"),
});

type AntiSurveySchema = z.infer<typeof formSchema>;

interface CustomerID {
	id: string;
	id_customer: string;
	name: string;
}

interface Driver {
	id: string;
	nik: string;
	name: string;
}

interface Answer {
    id: string;
    answer: string;
}

interface Props {
	title: string;
	subtitle: string;
    answers: Answer[];
}

export default function AntiSurvey({ title, subtitle, answers }: Props) {
	const { toast } = useToast();
	const form = useForm<AntiSurveySchema>({
		resolver: zodResolver(formSchema),
	});

	const fetchDrivers = async (searchTerm: string) => {
		const result = await axios.get<Driver[]>("/driver", {
			params: { search: searchTerm },
		});
		return result.data;
	};

	const fetchCustomerIds = async (searchTerm: string) => {
		const result = await axios.get<CustomerID[]>("/customer", {
			params: { search: searchTerm },
		});
		return result.data;
	};

	const [isSubmit, setIsSubmit] = useState(false);
	const [isSheetOpen, setIsSheetOpen] = useState(false);
	const [customCustomerId, setCustomCustomerId] = useState("");

	const handleSubmit = async (schema: AntiSurveySchema) => {
		setIsSubmit(true);

		const formData = new FormData();
		formData.append("customerId", schema.customerId);
		formData.append("driverId", schema.driverId);
		formData.append("answerId", schema.answerId);

		try {
			await axios.postForm("/decline-survey", formData);
			toast({
				title: "Berhasil",
				description: "Data berhasil dikirim",
				variant: "success",
			});

			form.reset();
		} catch (error) {
			if (axios.isAxiosError(error)) {
				switch (error.response?.status) {
					case 400:
						toast({
							variant: "destructive",
							title: "Gagal Memasukkan Data",
							description: error.response?.data?.message,
						});
						break;

					case 422:
						break;

					default:
						toast({
							variant: "destructive",
							title: "Gagal Memasukkan Data",
							description:
								error.response?.data?.message ??
								"Terdapat kesalahan pada server. Silakan coba beberapa saat lagi",
						});
						break;
				}
			}
		} finally {
			setIsSubmit(false);
		}
	};

	return (
		<div className="container mx-auto p-8">
			<Head title="Survey" />
			<Toaster />
			<h1 className="scroll-m-20 text-4xl font-extrabold tracking-tight lg:text-5xl text-center">
				{title}
			</h1>
			<h3 className="scroll-m-20 text-2xl font-semibold tracking-tight text-center">
				{subtitle}
			</h3>
			<Form {...form}>
				<form
					onSubmit={form.handleSubmit(handleSubmit)}
					className="border border-solid border-black space-y-4 max-w-3xl mx-auto py-10 p-4 mt-4"
				>
					<FormField
						control={form.control}
						name="customerId"
						disabled={isSubmit}
						render={({ field }) => (
							<>
								<Selector
									searchKey="customer"
									label="ID Customer"
									value={field.value}
									fetchItemsFn={fetchCustomerIds}
									onChange={field.onChange}
									renderDisplayOnFound={(items) => {
										if (!field.value) {
											return "Cari ID Customer";
										}

										const selectedItem = items.find(
											(item) => item.id === field.value,
										);
										if (!selectedItem) {
											return "Cari ID Customer";
										}

										return `${selectedItem.id_customer} (${selectedItem.name})`;
									}}
									renderDropdownList={(customer: CustomerID) =>
										`${customer.id_customer} (${customer.name})`
									}
								/>
								<Sheet open={isSheetOpen} onOpenChange={setIsSheetOpen}>
									<SheetTrigger asChild>
										<button className="text-blue-500 underline text-sm p-0 leading-none">
											ID Customer tidak ditemukan? Tambahkan disini
										</button>
									</SheetTrigger>
									<SheetContent side="bottom">
										<SheetHeader className="p-4">
											<SheetTitle>Masukkan Data Baru</SheetTitle>
										</SheetHeader>
										<SheetDescription>
											Pastikan sudah melakukan pencarian ID Customer terlebih
											dahulu sebelum memutuskan untuk mengisi ini.
										</SheetDescription>
										<div className="p-4 flex-1">
											<Input
												placeholder="Masukkan ID Customer"
												onChange={(e) => setCustomCustomerId(e.target.value)}
												value={customCustomerId}
											/>
											<Button
												className="mt-4"
												onClick={() => {
													form.setValue("customerId", customCustomerId);
													setIsSheetOpen(false);
												}}
											>
												Simpan
											</Button>
										</div>
									</SheetContent>
								</Sheet>
							</>
						)}
					/>
					<FormField
						control={form.control}
						name="driverId"
						render={({ field }) => (
							<Selector
								searchKey="driver"
								label="NIK Supir"
								value={field.value}
								fetchItemsFn={fetchDrivers}
								onChange={field.onChange}
								renderDisplayOnFound={(items) => {
									if (!field.value) {
										return "Cari NIK Supir";
									}

									const selectedItem = items.find(
										(item) => item.id === field.value,
									);
									if (!selectedItem) {
										return "Cari NIK Supir";
									}

									return `${selectedItem.nik} (${selectedItem.name})`;
								}}
								renderDropdownList={(driver: Driver) =>
									`${driver.nik} (${driver.name})`
								}
							/>
						)}
					/>
					<FormField
						control={form.control}
						name="answerId"
						render={({ field }) => (
                            <div>
                            <Label className="block mb-4">Alasan</Label>
                            <Select
                                onValueChange={field.onChange}
                                value={field.value}
                            >
                                <SelectTrigger ref={field.ref}>
                                    <SelectValue placeholder="Pilih Alasan" />
                                </SelectTrigger>
                                <SelectContent>
                                    {answers.map((answer) => (
                                        <SelectItem
                                            key={answer.id}
                                            value={answer.id}
                                        >
                                            {answer.answer}
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                            <FormMessage>
                                {form.formState.errors.answerId?.message}
                            </FormMessage>
                            </div>

						)}
					/>

					<Button
						type="submit"
						variant="destructive"
						className="w-full"
						disabled={isSubmit}
					>
						Kirim
					</Button>
				</form>
			</Form>
		</div>
	);
}
